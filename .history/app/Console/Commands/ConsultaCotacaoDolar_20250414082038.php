<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultaCotacaoDolar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consulta:cotacao-dolar {--data= : Data no formato YYYYMMDD (opcional, padrão: hoje)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta o preço atual do dólar utilizando a API do Banco Central';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Consultando cotação do dólar...');

        // Obtém a data do parâmetro ou usa a data atual
        $data = $this->option('data') ?: date('Ymd');
        
        try {
            // URL da API do Banco Central (AWESOMEAPI para cotações)
            $response = Http::get("https://economia.awesomeapi.com.br/json/daily/USD-BRL/1");
            
            if ($response->successful()) {
                $cotacao = $response->json();
                
                if (!empty($cotacao) && isset($cotacao[0])) {
                    $dadosCotacao = $cotacao[0];
                    
                    $this->info('=== Cotação do Dólar ===');
                    $this->info('Data: ' . date('d/m/Y', strtotime($dadosCotacao['create_date'])));
                    $this->info('Valor de Compra: R$ ' . $dadosCotacao['bid']);
                    $this->info('Valor de Venda: R$ ' . $dadosCotacao['ask']);
                    $this->info('Máxima: R$ ' . $dadosCotacao['high']);
                    $this->info('Mínima: R$ ' . $dadosCotacao['low']);
                    
                    return Command::SUCCESS;
                } else {
                    $this->error('Nenhum dado de cotação encontrado para a data especificada.');
                    return Command::FAILURE;
                }
            } else {
                $this->error('Erro ao consultar a API: ' . $response->status());
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Erro ao consultar cotação do dólar: ' . $e->getMessage());
            Log::error('Erro ao consultar cotação do dólar: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 