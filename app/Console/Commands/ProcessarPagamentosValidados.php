<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Shipment;
use App\Services\FedexService;
use Illuminate\Support\Facades\Log;

class ProcessarPagamentosValidados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:processar-pagamentos-validados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa pagamentos validados e realiza o envio para a FedEx';

    /**
     * O serviço FedEx que será utilizado.
     *
     * @var \App\Services\FedexService
     */
    protected $fedexService;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\FedexService $fedexService
     * @return void
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
        $this->info('Iniciando processamento de pagamentos validados...');
        
        // Buscar pagamentos confirmados que ainda não foram processados na FedEx
        $pagamentos = Payment::whereIn('status', ['confirmed', 'received'])
            ->whereHas('shipment', function ($query) {
                $query->whereNull('tracking_number')
                      ->where('status', 'pending');
            })
            ->with(['shipment.senderAddress', 'shipment.recipientAddress', 'shipment.items'])
            ->limit(10) // Processa em lotes para evitar sobrecarga
            ->get();
        
        $this->info("Encontrados {$pagamentos->count()} pagamentos para processar.");
        
        if ($pagamentos->isEmpty()) {
            $this->info('Nenhum pagamento pendente para processar.');
            return 0;
        }
        
        foreach ($pagamentos as $pagamento) {
            $this->info("Processando pagamento #{$pagamento->id} para envio #{$pagamento->shipment_id}");
            
            try {
                // Obter dados do envio
                $shipment = $pagamento->shipment;
                
                if (!$shipment) {
                    $this->error("Envio não encontrado para o pagamento #{$pagamento->id}");
                    continue;
                }
                
                // Preparar dados para envio à FedEx
                $dadosRemetente = $this->prepararDadosRemetente($shipment);
                $dadosDestinatario = $this->prepararDadosDestinatario($shipment);
                $dadosPacote = $this->prepararDadosPacote($shipment);
                $dadosProdutos = $this->prepararDadosProdutos($shipment);
                
                // Log para debug
                Log::info('ProcessarPagamentosValidados: Dados preparados para envio à FedEx', [
                    'payment_id' => $pagamento->id,
                    'shipment_id' => $shipment->id,
                    'tracking_number' => $shipment->tracking_number,
                    'remetente' => $dadosRemetente,
                    'destinatario' => $dadosDestinatario,
                    'pacote' => $dadosPacote,
                    'produtos' => $dadosProdutos,
                    'servico' => $shipment->service_code
                ]);
                
                // Enviar para a FedEx
                $response = $this->fedexService->criarEnvio(
                    $dadosRemetente,
                    $dadosDestinatario,
                    $dadosPacote,
                    $dadosProdutos,
                    $shipment->service_code
                );
                
                // Log da resposta
                Log::info('ProcessarPagamentosValidados: Resposta da FedEx', [
                    'payment_id' => $pagamento->id,
                    'shipment_id' => $shipment->id,
                    'response' => $response
                ]);
                
                if ($response['success']) {
                    // Atualizar o envio com os dados de rastreamento
                    $shipment->tracking_number = $response['tracking_number'];
                    $shipment->shipment_id = $response['shipment_id'];
                    $shipment->label_url = $response['label_url'];
                    $shipment->status = 'created';
                    $shipment->status_description = 'Envio criado e pronto para despacho';
                    $shipment->save();
                    
                    $this->info("Envio processado com sucesso. Tracking: {$response['tracking_number']}");
                } else {
                    $shipment->status = 'error';
                    $shipment->status_description = 'Erro ao processar envio: ' . ($response['message'] ?? 'Erro desconhecido');
                    $shipment->save();
                    
                    $this->error("Erro ao processar o envio: " . ($response['message'] ?? 'Erro desconhecido'));
                }
                
            } catch (\Exception $e) {
                Log::error('ProcessarPagamentosValidados: Erro ao processar pagamento', [
                    'payment_id' => $pagamento->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $this->error("Erro ao processar pagamento #{$pagamento->id}: " . $e->getMessage());
            }
        }
        
        $this->info('Processamento de pagamentos concluído.');
        return 0;
    }
    
    /**
     * Prepara os dados do remetente para o envio
     */
    private function prepararDadosRemetente($shipment)
    {
        $senderAddress = $shipment->senderAddress;
        
        if (!$senderAddress) {
            throw new \Exception("Endereço do remetente não encontrado");
        }
        
        return [
            'name' => $senderAddress->name,
            'phone' => $senderAddress->phone,
            'email' => $senderAddress->email,
            'address' => $senderAddress->address,
            'complement' => $senderAddress->address_complement,
            'city' => $senderAddress->city,
            'state' => $senderAddress->state,
            'postalCode' => $senderAddress->postal_code,
            'country' => $senderAddress->country,
            'isResidential' => $senderAddress->is_residential
        ];
    }
    
    /**
     * Prepara os dados do destinatário para o envio
     */
    private function prepararDadosDestinatario($shipment)
    {
        $recipientAddress = $shipment->recipientAddress;
        
        if (!$recipientAddress) {
            throw new \Exception("Endereço do destinatário não encontrado");
        }
        
        return [
            'name' => $recipientAddress->name,
            'phone' => $recipientAddress->phone,
            'email' => $recipientAddress->email,
            'address' => $recipientAddress->address,
            'complement' => $recipientAddress->address_complement,
            'city' => $recipientAddress->city,
            'state' => $recipientAddress->state,
            'postalCode' => $recipientAddress->postal_code,
            'country' => $recipientAddress->country,
            'isResidential' => $recipientAddress->is_residential
        ];
    }
    
    /**
     * Prepara os dados do pacote para o envio
     */
    private function prepararDadosPacote($shipment)
    {
        return [
            'height' => $shipment->package_height,
            'width' => $shipment->package_width,
            'length' => $shipment->package_length,
            'weight' => $shipment->package_weight,
            'packageCount' => 1
        ];
    }
    
    /**
     * Prepara os dados dos produtos para o envio
     */
    private function prepararDadosProdutos($shipment)
    {
        $produtos = [];
        
        foreach ($shipment->items as $item) {
            $produtos[] = [
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unitPrice' => $item->unit_price,
                'weight' => $item->weight,
                'harmonizedCode' => $item->harmonized_code,
                'countryOfOrigin' => $item->country_of_origin ?? 'BR'
            ];
        }
        
        return $produtos;
    }
} 