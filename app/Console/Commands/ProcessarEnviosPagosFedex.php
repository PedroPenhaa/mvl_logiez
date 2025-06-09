<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\EnvioController;
use Illuminate\Http\Request;

class ProcessarEnviosPagosFedex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processar:envios-fedex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa todos os pagamentos CONFIRMED e envia para a FedEx, salvando os retornos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando processamento de envios pagos para FedEx...');
        $controller = new EnvioController(app(\App\Services\FedexService::class));
        $request = new Request();
        $response = $controller->processarEnviosPagosFedex($request);
        $data = $response->getData(true);
        if ($data['success']) {
            $this->info('Processamento concluído. Resultados:');
            foreach ($data['resultados'] as $resultado) {
                $this->line(json_encode($resultado));
            }
        } else {
            $this->error('Erro ao processar: ' . ($data['mensagem'] ?? 'Erro desconhecido'));
        }
        return 0;
    }
} 