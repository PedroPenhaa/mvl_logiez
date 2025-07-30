<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConverterFreteUSD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'converter:frete-usd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converte valores de frete de USD para BRL';

    // Valores de frete em USD
    protected $valoresFreteUSD = [
        'FedEx International First®' => 531.81,
        'FedEx International Economy®' => 424.77,
        'FedEx International Priority®' => 442.99,
        'FedEx International Connect Plus' => 373.48,
        'FedEx International Priority® Express' => 464.36
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Consultando cotação do dólar...');

        try {
            // URL da API AwesomeAPI para cotações
            $response = Http::get("https://economia.awesomeapi.com.br/json/daily/USD-BRL/1");
            
            if ($response->successful()) {
                $cotacao = $response->json();
                
                if (!empty($cotacao) && isset($cotacao[0])) {
                    $dadosCotacao = $cotacao[0];
                    $cotacaoVenda = floatval($dadosCotacao['ask']);
                    
                    $this->info('=== Cotação do Dólar ===');
                    $this->info('Data: ' . date('d/m/Y', strtotime($dadosCotacao['create_date'])));
                    $this->info('Valor de Venda: R$ ' . number_format($cotacaoVenda, 2, ',', '.'));
                    
                    $this->info('');
                    $this->info('=== Valores de Frete Convertidos ===');
                    
                    $this->table(
                        ['Serviço', 'Valor (USD)', 'Valor (BRL)'],
                        $this->prepararDadosTabela($cotacaoVenda)
                    );
                    
                    return Command::SUCCESS;
                } else {
                    $this->error('Nenhum dado de cotação encontrado.');
                    return Command::FAILURE;
                }
            } else {
                $this->error('Erro ao consultar a API: ' . $response->status());
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Erro ao consultar cotação do dólar: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Prepara os dados para a tabela
     */
    private function prepararDadosTabela($cotacao)
    {
        $dadosTabela = [];
        
        foreach ($this->valoresFreteUSD as $servico => $valorUSD) {
            $valorBRL = $valorUSD * $cotacao;
            
            $dadosTabela[] = [
                'servico' => $servico,
                'valor_usd' => 'USD ' . number_format($valorUSD, 2, ',', '.'),
                'valor_brl' => 'R$ ' . number_format($valorBRL, 2, ',', '.')
            ];
        }
        
        return $dadosTabela;
    }
} 