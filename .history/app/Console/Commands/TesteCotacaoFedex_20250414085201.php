<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FedexService;
use Illuminate\Support\Facades\Log;

class TesteCotacaoFedex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teste:cotacao-fedex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a API de cotação da FedEx e exibe os detalhes da resposta';

    protected $fedexService;

    /**
     * Create a new command instance.
     */
    public function __construct(FedexService $fedexService)
    {
        parent::__construct();
        $this->fedexService = $fedexService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando teste de cotação FedEx...');
        
        // Parâmetros de teste
        $origem = '01310-100'; // São Paulo
        $destino = '10001'; // New York
        $altura = 10; // cm
        $largura = 20; // cm
        $comprimento = 30; // cm
        $peso = 1; // kg
        
        // Testar simulação
        $this->info('Testando com simulação forçada:');
        try {
            $simulado = $this->fedexService->calcularCotacao(
                $origem,
                $destino,
                $altura,
                $largura,
                $comprimento,
                $peso,
                true // Forçar simulação
            );
            
            $this->info('Resposta da simulação:');
            $this->info(json_encode($simulado, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $this->error('Erro na simulação: ' . $e->getMessage());
            Log::error('Erro no teste de simulação: ' . $e->getMessage());
        }
        
        // Testar API real
        $this->info('Testando com API real:');
        try {
            // Registrar token
            $token = $this->fedexService->getAuthToken(true);
            $this->info('Token obtido: ' . substr($token, 0, 10) . '...' . substr($token, -10));
            
            // Calcular cotação
            $resultado = $this->fedexService->calcularCotacao(
                $origem,
                $destino,
                $altura,
                $largura,
                $comprimento,
                $peso,
                false // Usar API real
            );
            
            $this->info('Resposta da API:');
            $this->info(json_encode($resultado, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $this->error('Erro na API real: ' . $e->getMessage());
            Log::error('Erro no teste de API real: ' . $e->getMessage(), [
                'origem' => $origem,
                'destino' => $destino,
                'dimensoes' => compact('altura', 'largura', 'comprimento', 'peso')
            ]);
        }
        
        return Command::SUCCESS;
    }
} 