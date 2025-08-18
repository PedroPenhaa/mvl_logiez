<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB; // Added DB facade

class EtiquetaController extends Controller
{
    public function index()
    {
        return view('etiqueta.index');
    }
    
    public function gerar(Request $request)
    {
        // Validação dos dados para geração da etiqueta
        $validated = $request->validate([
            'codigo_envio' => 'required|string',
        ]);
        
        // Simulação de integração com API da DHL para gerar etiqueta
        // Em produção, aqui seria feita a chamada real à API
        
        // Simulamos um link fictício para download da etiqueta
        $linkEtiqueta = "https://exemplo.com/etiquetas/12345.pdf";
        
        return view('etiqueta.exibir', [
            'link_etiqueta' => $linkEtiqueta,
            'codigo_envio' => $validated['codigo_envio']
        ]);
    }

    /**
     * Consulta etiqueta FedEx em tempo real e retorna a URL do PDF.
     */
    public function fedex(Request $request)
    {
        try {
            // Validar o código de rastreamento
            $request->validate([
                'codigo' => 'required'
            ]);

            // Converter o código para string se necessário
            $codigo = (string) $request->codigo;

            // Buscar dados do shipment e tabelas relacionadas
            $shipment = DB::table('shipments')
                ->where('tracking_number', $codigo)
                ->first();

            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment não encontrado com este tracking number'
                ], 404);
            }

            // Buscar dados do recipient_addresses
            $recipientAddress = DB::table('recipient_addresses')
                ->where('shipment_id', $shipment->id)
                ->first();

            // Buscar dados do sender_addresses
            $senderAddress = DB::table('sender_addresses')
                ->where('shipment_id', $shipment->id)
                ->first();

            // Buscar dados do shipment_items
            $shipmentItems = DB::table('shipment_items')
                ->where('shipment_id', $shipment->id)
                ->get();

            // Montar o JSON com todos os dados
            $dados = [
                'shipment' => $shipment,
                'recipient_address' => $recipientAddress,
                'sender_address' => $senderAddress,
                'shipment_items' => $shipmentItems
            ];

            // 1. Autenticar na FedEx
            $auth = Http::asForm()->post(config('services.fedex.api_url') . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.fedex.client_id'),
                'client_secret' => config('services.fedex.client_secret'),
            ]);
            
            $accessToken = $auth->json()['access_token'] ?? null;
            if (!$accessToken) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Erro ao autenticar na FedEx'
                ], 500);
            }

            // 2. Montar o JSON com dados dinâmicos do banco
            $body = [
                "labelResponseOptions" => "URL_ONLY",
                "accountNumber" => ["value" => env('FEDEX_PROD_SHIPPER_ACCOUNT', '207227690')],
                "requestedShipment" => [
                    "shipDatestamp" => $shipment->ship_date,
                    "serviceType" => $shipment->service_code,
                    "packagingType" => "YOUR_PACKAGING",
                    "pickupType" => "USE_SCHEDULED_PICKUP",
                    "blockInsightVisibility" => false,
                    "shipper" => [
                        "contact" => [
                            "personName" => $senderAddress->name,
                            "phoneNumber" => preg_replace('/\D/', '', $senderAddress->phone),
                           
                        ],
                        "address" => [
                            "streetLines" => [
                                $senderAddress->address,
                                $senderAddress->address_complement
                            ],
                            "city" => $senderAddress->city,
                            "stateOrProvinceCode" => $senderAddress->state,
                            "postalCode" => $senderAddress->postal_code,
                            "countryCode" => $senderAddress->country
                        ]
                    ],
                    "recipients" => [[
                        "contact" => [
                            "personName" => $recipientAddress->name,
                            "phoneNumber" => preg_replace('/\D/', '', $recipientAddress->phone),
                            "companyName" => substr($recipientAddress->name, 0, 30)
                        ],
                        "address" => [
                            "streetLines" => [
                                $recipientAddress->address,
                                $recipientAddress->address_complement
                            ],
                            "city" => $recipientAddress->city,
                            "stateOrProvinceCode" => $recipientAddress->state,
                            "postalCode" => $recipientAddress->postal_code,
                            "countryCode" => $recipientAddress->country,
                            "residential" => (bool) $recipientAddress->is_residential
                        ]
                    ]],
                    "shippingChargesPayment" => [
                        "paymentType" => "SENDER"
                    ],
                    "labelSpecification" => [
                        "imageType" => "PDF",
                        "labelStockType" => "STOCK_4X6"
                    ],
                    "customsClearanceDetail" => [
                        "dutiesPayment" => ["paymentType" => "RECIPIENT"],
                        "isDocumentOnly" => false,
                        "commodities" => $shipmentItems->map(function($item) {
                            return [
                                "description" => $item->description,
                                "countryOfManufacture" => $item->country_of_origin ?? "BR",
                                "harmonizedCode" => $item->harmonized_code,
                                "quantity" => $item->quantity,
                                "quantityUnits" => "PCS",
                                "unitPrice" => [
                                    "amount" => (float) $item->unit_price,
                                    "currency" => $item->currency
                                ],
                                "customsValue" => [
                                    "amount" => (float) $item->total_price,
                                    "currency" => $item->currency
                                ],
                                "weight" => [
                                    "units" => "KG",
                                    "value" => (float) $item->weight
                                ]
                            ];
                        })->toArray()
                    ],
                    "shippingDocumentSpecification" => [
                        "shippingDocumentTypes" => ["COMMERCIAL_INVOICE"],
                        "commercialInvoiceDetail" => [
                            "documentFormat" => [
                                "stockType" => "PAPER_LETTER",
                                "docType" => "PDF"
                            ]
                        ]
                    ],
                    "requestedPackageLineItems" => [[
                        "weight" => [
                            "units" => "KG",
                            "value" => (float) $shipment->package_weight
                        ]
                    ]]
                ]
            ];

            // 3. Chamar a API de etiqueta
            $response = Http::withToken($accessToken)
                ->post(config('services.fedex.api_url') . config('services.fedex.ship_endpoint', '/ship/v1/shipments'), $body);

            if ($response->failed()) {
                $responseData = $response->json();
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Erro ao gerar etiqueta', 
                    'fedex' => $responseData
                ], 500);
            }

            $responseData = $response->json();
            $shipmentData = $responseData['output']['transactionShipments'][0] ?? null;

            if (!$shipmentData) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Dados da etiqueta não encontrados'
                ], 404);
            }

            // 4. Extrair dados relevantes
            $labelUrl = $shipmentData['pieceResponses'][0]['packageDocuments'][0]['url'] ?? null;
            $trackingNumber = $shipmentData['masterTrackingNumber'] ?? null;
            $serviceName = $shipmentData['serviceName'] ?? 'International Priority';
            $shipDate = $shipmentData['shipDatestamp'] ?? date('Y-m-d');
            $recipient = $shipmentData['recipients'][0] ?? null;
            $recipientName = $recipient['contact']['personName'] ?? 'N/A';
            $recipientCity = $recipient['address']['city'] ?? 'N/A';
            $recipientCountry = $recipient['address']['countryCode'] ?? 'N/A';

            return response()->json([
                'success' => true,
                'labelUrl' => $labelUrl,
                'trackingNumber' => $trackingNumber,
                'serviceName' => $serviceName,
                'shipDate' => $shipDate,
                'recipient' => [
                    'name' => $recipientName,
                    'city' => $recipientCity,
                    'country' => $recipientCountry
                ],
                'dados' => $dados
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a requisição: ' . $e->getMessage()
            ], 500);
        }
    }
} 