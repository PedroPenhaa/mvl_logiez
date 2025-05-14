<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shipment;
use App\Models\Payment;
use App\Http\Controllers\EnvioController;
use App\Services\FedexService;
use Illuminate\Support\Facades\Log;

class ProcessarEnviosPendentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:processar-envios-pendentes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa envios pendentes que já tiveram o pagamento confirmado';

    /**
     * Serviço FedEx
     */
    protected $fedexService;

    /**
     * Constructor
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
        $this->info('Iniciando processamento de envios pendentes...');
        
        // Buscar envios com status 'pending_payment'
        $envios = Shipment::where('status', 'pending_payment')
            ->limit(10) // Processar em lotes
            ->get();
        
        $this->info("Encontrados {$envios->count()} envios pendentes.");
        
        if ($envios->isEmpty()) {
            $this->info('Nenhum envio pendente para processar.');
            return 0;
        }
        
        foreach ($envios as $envio) {
            $this->info("Verificando envio #{$envio->id}");
            
            try {
                // Buscar o pagamento associado a este envio
                $pagamento = Payment::where('shipment_id', $envio->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if (!$pagamento) {
                    $this->warn("Envio #{$envio->id} não possui pagamento associado.");
                    continue;
                }
                
                $this->info("Pagamento #{$pagamento->id} encontrado com status: {$pagamento->status}");
                
                // Verificar se o pagamento foi confirmado
                if (in_array($pagamento->status, ['confirmed', 'received'])) {
                    $this->info("Processando envio #{$envio->id} (pagamento confirmado)");
                    
                    // Processar o envio na FedEx usando nosso próprio serviço injetado
                    $dadosRemetente = [
                        'name' => $envio->senderAddress->name,
                        'phone' => $envio->senderAddress->phone,
                        'email' => $envio->senderAddress->email,
                        'address' => $envio->senderAddress->address,
                        'complement' => $envio->senderAddress->address_complement,
                        'city' => $envio->senderAddress->city,
                        'state' => $envio->senderAddress->state,
                        'postalCode' => $envio->senderAddress->postal_code,
                        'country' => $envio->senderAddress->country,
                        'isResidential' => $envio->senderAddress->is_residential
                    ];
                    
                    $dadosDestinatario = [
                        'name' => $envio->recipientAddress->name,
                        'phone' => $envio->recipientAddress->phone,
                        'email' => $envio->recipientAddress->email,
                        'address' => $envio->recipientAddress->address,
                        'complement' => $envio->recipientAddress->address_complement,
                        'city' => $envio->recipientAddress->city,
                        'state' => $envio->recipientAddress->state,
                        'postalCode' => $envio->recipientAddress->postal_code,
                        'country' => $envio->recipientAddress->country,
                        'isResidential' => $envio->recipientAddress->is_residential
                    ];
                    
                    $dadosPacote = [
                        'height' => $envio->package_height,
                        'width' => $envio->package_width,
                        'length' => $envio->package_length,
                        'weight' => $envio->package_weight,
                        'packageCount' => 1
                    ];
                    
                    $dadosProdutos = [];
                    foreach ($envio->items as $item) {
                        $dadosProdutos[] = [
                            'description' => $item->description,
                            'quantity' => $item->quantity,
                            'unitPrice' => $item->unit_price,
                            'weight' => $item->weight,
                            'harmonizedCode' => $item->harmonized_code,
                            'countryOfOrigin' => $item->country_of_origin ?? 'BR'
                        ];
                    }
                    
                    $respostaFedex = $this->fedexService->criarEnvio(
                        $dadosRemetente,
                        $dadosDestinatario,
                        $dadosPacote,
                        $dadosProdutos,
                        $envio->service_code
                    );
                    
                    if ($respostaFedex['success']) {
                        // Atualizar o envio com os dados da FedEx
                        $envio->tracking_number = $respostaFedex['tracking_number'];
                        $envio->shipment_id = $respostaFedex['shipment_id'];
                        $envio->shipping_label_url = $respostaFedex['label_url'] ?? null;
                        $envio->status = 'created';
                        $envio->save();
                        
                        $this->info("Envio #{$envio->id} processado com sucesso! Tracking: {$envio->tracking_number}");
                        
                        // Registrar no log
                        Log::info('ProcessarEnviosPendentes: Envio processado com sucesso', [
                            'shipment_id' => $envio->id,
                            'tracking_number' => $envio->tracking_number,
                            'payment_id' => $pagamento->id
                        ]);
                    } else {
                        $this->error("Erro ao processar envio #{$envio->id} na FedEx: " . ($respostaFedex['message'] ?? 'Erro desconhecido'));
                        
                        // Registrar o erro
                        Log::error('ProcessarEnviosPendentes: Erro ao processar envio na FedEx', [
                            'shipment_id' => $envio->id,
                            'payment_id' => $pagamento->id,
                            'error' => $respostaFedex['message'] ?? 'Erro desconhecido'
                        ]);
                    }
                } else {
                    $this->info("Envio #{$envio->id} com pagamento ainda não confirmado (status: {$pagamento->status})");
                }
                
            } catch (\Exception $e) {
                $this->error("Erro ao processar envio #{$envio->id}: " . $e->getMessage());
                
                // Registrar o erro
                Log::error('ProcessarEnviosPendentes: Erro ao processar envio', [
                    'shipment_id' => $envio->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info('Processamento de envios pendentes concluído.');
        return 0;
    }
}
