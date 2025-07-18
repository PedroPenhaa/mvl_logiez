<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
                'codigo' => 'required|string'
            ]);

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

            // 2. Montar o JSON conforme exemplo do usuário (ajuste conforme necessário)
            $body = [
                "labelResponseOptions" => "URL_ONLY",
                "accountNumber" => ["value" => "740561073"],
                "requestedShipment" => [
                    "shipDatestamp" => date('Y-m-d'),
                    "serviceType" => "INTERNATIONAL_PRIORITY",
                    "packagingType" => "YOUR_PACKAGING",
                    "pickupType" => "USE_SCHEDULED_PICKUP",
                    "blockInsightVisibility" => false,
                    "shipper" => [
                        "tins" => [[
                            "number" => "GB123456789",
                            "tinType" => "BUSINESS_UNION",
                            "usage" => "usage",
                            "effectiveDate" => "2000-01-23T04:56:07.000+00:00",
                            "expirationDate" => "2024-01-23T04:56:07.000+00:00"
                        ]],
                        "contact" => [
                            "personName" => "SHIPPER NAME",
                            "phoneNumber" => 1234567890,
                            "companyName" => "Shipper Company Name"
                        ],
                        "address" => [
                            "streetLines" => [
                                "SHIPPER STREET LINE 1",
                                "SHIPPER STREET LINE 2",
                                "SHIPPER STREET LINE 3"
                            ],
                            "city" => "MIAMI",
                            "stateOrProvinceCode" => "FL",
                            "postalCode" => "33126",
                            "countryCode" => "US"
                        ]
                    ],
                    "recipients" => [[
                        "contact" => [
                            "personName" => "RECIPIENT NAME",
                            "phoneNumber" => 1234567890,
                            "companyName" => "Recipient Company Name"
                        ],
                        "address" => [
                            "streetLines" => [
                                "RECIPIENT STREET LINE 1",
                                "RECIPIENT STREET LINE 2",
                                "RECIPIENT STREET LINE 3"
                            ],
                            "city" => "SAO PAULO",
                            "stateOrProvinceCode" => "SP",
                            "postalCode" => "01138000",
                            "countryCode" => "BR",
                            "residential" => true
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
                        "commercialInvoice" => [
                            "originatorName" => "originator Name",
                            "customerReferences" => [[
                                "customerReferenceType" => "INVOICE_NUMBER",
                                "value" => "3686"
                            ]],
                            "packingCosts" => ["amount" => 12.45, "currency" => "USD"],
                            "handlingCosts" => ["amount" => 12.45, "currency" => "USD"],
                            "freightCharge" => ["amount" => 12.45, "currency" => "USD"],
                            "insuranceCharge" => ["amount" => 12.45, "currency" => "USD"],
                            "declarationStatement" => "declarationStatement",
                            "termsOfSale" => "FCA",
                            "specialInstructions" => "specialInstructions",
                            "shipmentPurpose" => "SOLD"
                        ],
                        "dutiesPayment" => ["paymentType" => "RECIPIENT"],
                        "isDocumentOnly" => false,
                        "commodities" => [[
                            "description" => "MUSICAL INSTRUMENTS",
                            "countryOfManufacture" => "CN",
                            "harmonizedCode" => "90189084",
                            "quantity" => 1,
                            "quantityUnits" => "PCS",
                            "unitPrice" => ["amount" => 1000, "currency" => "USD"],
                            "customsValue" => ["amount" => 1000, "currency" => "USD"],
                            "weight" => ["units" => "KG", "value" => 0.01]
                        ]]
                    ],
                    "shippingDocumentSpecification" => [
                        "shippingDocumentTypes" => ["COMMERCIAL_INVOICE"],
                        "commercialInvoiceDetail" => [
                            "documentFormat" => [
                                "stockType" => "PAPER_LETTER",
                                "docType" => "PDF"
                            ],
                            "dispositions" => [["dispositionType" => "RETURNED"]]
                        ]
                    ],
                    "edtRequestType" => "ALL",
                    "requestedPackageLineItems" => [[
                        "customerReferences" => [[
                            "customerReferenceType" => "CUSTOMER_REFERENCE",
                            "value" => $request->codigo
                        ]],
                        "weight" => ["units" => "KG", "value" => 0.01]
                    ]],
                    "rateRequestType" => ["LIST", "PREFERRED"]
                ]
            ];

            // 3. Chamar a API de etiqueta
            $response = Http::withToken($accessToken)
                ->post(config('services.fedex.api_url') . config('services.fedex.ship_endpoint', '/ship/v1/shipments'), $body);

            if ($response->failed()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Erro ao gerar etiqueta', 
                    'fedex' => $response->json()
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
                'dados' => $shipmentData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a requisição: ' . $e->getMessage()
            ], 500);
        }
    }
} 