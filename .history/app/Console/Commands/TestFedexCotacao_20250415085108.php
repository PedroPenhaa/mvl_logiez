<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FedexService;
use Illuminate\Support\Facades\Log;

class TestFedexCotacao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fedex:test-cotacao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a API de cotação da FedEx';

    /**
     * Execute the console command.
     */
    public function handle(FedexService $fedexService)
    {
        $this->info('🚚 Testando cotação de envio FedEx');
        $this->info('----------------------------------');

        // Valores fixos para o teste
        $origem = '01310100';    // CEP de São Paulo (Av. Paulista)
        $destino = '33131';      // CEP de Miami, FL
        $altura = 10;            // 10 cm
        $largura = 20;           // 20 cm
        $comprimento = 30;       // 30 cm
        $peso = 10;              // 10 kg
        
        // Forçar simulação desativado - vamos usar a API real
        $forcarSimulacao = false;

        $this->info("📦 Dados do pacote:");
        $this->info("Origem: $origem");
        $this->info("Destino: $destino");
        $this->info("Dimensões: {$altura}cm x {$largura}cm x {$comprimento}cm");
        $this->info("Peso: {$peso}kg");
        $this->info("Usando API real (não simulada)");

        $this->newLine();
        $this->info('⏳ Enviando requisição para calcular cotação...');
        
        try {
            // Fazer a requisição através do FedexService
            $resultado = $fedexService->calcularCotacao(
                $origem,
                $destino,
                $altura,
                $largura,
                $comprimento,
                $peso,
                $forcarSimulacao
            );

            // Imprimir informações do peso
            $this->newLine();
            $this->info('✅ Cotação calculada com sucesso!');
            $this->info('----------------------------------');
            $this->info("Peso Cúbico: {$resultado['pesoCubico']} kg");
            $this->info("Peso Real: {$resultado['pesoReal']} kg");
            $this->info("Peso Utilizado: {$resultado['pesoUtilizado']} kg");
            $this->info("Simulado: " . ($resultado['simulado'] ? 'Sim' : 'Não'));
            
            if (isset($resultado['mensagem'])) {
                $this->warn("Mensagem: {$resultado['mensagem']}");
            }

            // Imprimir resultados das cotações
            $this->newLine();
            $this->info('📋 Opções de envio encontradas: ' . count($resultado['cotacoesFedEx']));
            
            if (count($resultado['cotacoesFedEx']) > 0) {
                $this->table(
                    ['Serviço', 'Valor', 'Moeda', 'Tempo de Entrega', 'Data Estimada'],
                    array_map(function($cotacao) {
                        return [
                            $cotacao['servico'],
                            $cotacao['valorTotal'],
                            $cotacao['moeda'],
                            $cotacao['tempoEntrega'] ?? 'N/A',
                            $cotacao['dataEntrega'] ?? 'N/A'
                        ];
                    }, $resultado['cotacoesFedEx'])
                );
            } else {
                $this->error('Nenhuma opção de envio encontrada!');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erro ao calcular cotação: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            
            Log::error('Erro no command TestFedexCotacao', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
} 