<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConverterUSDparaBRL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'converter:usd-brl {valores?* : Valores em USD para converter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converte valores de USD para BRL usando a cotação atual';

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
                    
                    // Obter valores USD dos argumentos
                    $valoresUSD = $this->argument('valores');
                    
                    if (empty($valoresUSD)) {
                        // Se não houver valores fornecidos, solicitar ao usuário
                        $this->info('');
                        $this->info('Digite valores em USD para converter (separados por espaço):');
                        $inputValores = $this->ask('Valores USD');
                        $valoresUSD = explode(' ', $inputValores);
                    }
                    
                    $this->info('');
                    $this->info('=== Conversão de USD para BRL ===');
                    
                    foreach ($valoresUSD as $valorUSD) {
                        $valorUSD = floatval(str_replace(',', '.', $valorUSD));
                        $valorBRL = $valorUSD * $cotacaoVenda;
                        
                        $this->info(
                            number_format($valorUSD, 2, ',', '.') . ' USD = R$ ' . 
                            number_format($valorBRL, 2, ',', '.')
                        );
                    }
                    
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
} 