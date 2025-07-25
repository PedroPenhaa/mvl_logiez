<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Shipment; // Added this import for Shipment model

class FedexController extends Controller
{
    private $fedexApiUrl = 'https://apis.fedex.com';

    public function auth(Request $request)
    {
        try {
            // 1. Autenticação na FedEx
            $auth = Http::asForm()->post(config('services.fedex.api_url') . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.fedex.client_id'),
                'client_secret' => config('services.fedex.client_secret'),
            ]);

            if ($auth->successful()) {
                return response()->json([
                    'success' => true,
                    'access_token' => $auth->json()['access_token']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao autenticar com a FedEx',
                'error' => $auth->json()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao autenticar com a FedEx',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getEtiqueta(Request $request)
    {
        try {
            $trackingNumber = $request->codigo;

            // 1. Autenticação na FedEx
            $auth = Http::asForm()->post(config('services.fedex.api_url') . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.fedex.client_id'),
                'client_secret' => config('services.fedex.client_secret'),
            ]);

            // Log da resposta de autenticação
            Log::info('Resposta de autenticação FedEx:', [
                'status' => $auth->status(),
                'body' => $auth->json()
            ]);

            if (!$auth->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao autenticar na FedEx: ' . ($auth->json()['errors'][0]['message'] ?? 'Erro desconhecido'),
                    'error' => $auth->json()
                ], 401);
            }

            $accessToken = $auth->json()['access_token'] ?? null;
            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de acesso não encontrado na resposta da FedEx'
                ], 401);
            }

            // Log do token obtido (apenas para debug - remover em produção)
            Log::info('Token de acesso obtido:', ['token' => $accessToken]);

            // 2. Montar o corpo da requisição para a etiqueta
            $body = [
                "labelResponseOptions" => "URL_ONLY",
                "mergeLabelDocOption" => "LABELS_ONLY",
                "accountNumber" => [
                    "value" => "207227690"
                ],
                "requestedShipment" => [
                    "shipDatestamp" => now()->format('Y-m-d'),
                    "totalDeclaredValue" => [
                        "amount" => 100,
                        "currency" => "USD"
                    ],
                    "shipper" => [
                        "address" => [
                            "streetLines" => [
                                "Rua 4, Pq Res. Dona Chiquinha"
                            ],
                            "city" => "Cosmópolis",
                            "stateOrProvinceCode" => "SP",
                            "postalCode" => "13150000",
                            "countryCode" => "BR",
                            "residential" => false
                        ],
                        "contact" => [
                            "personName" => "LS COMÉRCIO",
                            "phoneNumber" => "19981166445",
                            "companyName" => "LS COMÉRCIO"
                        ]
                    ],
                    "recipients" => [[
                        "address" => [
                            "streetLines" => [
                                "123 Main St"
                            ],
                            "city" => "Miami",
                            "stateOrProvinceCode" => "FL",
                            "postalCode" => "33126",
                            "countryCode" => "US",
                            "residential" => true
                        ],
                        "contact" => [
                            "personName" => "RECIPIENT NAME",
                            "phoneNumber" => "1234567890",
                            "companyName" => "Recipient Company"
                        ]
                    ]],
                    "serviceType" => "INTERNATIONAL_PRIORITY",
                    "packagingType" => "YOUR_PACKAGING",
                    "pickupType" => "USE_SCHEDULED_PICKUP",
                    "shippingChargesPayment" => [
                        "paymentType" => "SENDER",
                        "payor" => [
                            "responsibleParty" => [
                                "accountNumber" => [
                                    "value" => "207227690"
                                ]
                            ]
                        ]
                    ],
                    "labelSpecification" => [
                        "imageType" => "PDF",
                        "labelStockType" => "STOCK_4X6"
                    ],
                    "customsClearanceDetail" => [
                        "dutiesPayment" => [
                            "paymentType" => "SENDER",
                            "payor" => [
                                "responsibleParty" => [
                                    "accountNumber" => [
                                        "value" => "207227690"
                                    ]
                                ]
                            ]
                        ],
                        "commodities" => [[
                            "description" => "Sample Product",
                            "countryOfManufacture" => "BR",
                            "quantity" => 1,
                            "quantityUnits" => "PCS",
                            "unitPrice" => [
                                "amount" => 100,
                                "currency" => "USD"
                            ],
                            "customsValue" => [
                                "amount" => 100,
                                "currency" => "USD"
                            ],
                            "weight" => [
                                "units" => "KG",
                                "value" => 1
                            ]
                        ]]
                    ],
                    "masterTrackingId" => [
                        "trackingNumber" => $trackingNumber,
                        "trackingIdType" => "FEDEX"
                    ],
                    "requestedPackageLineItems" => [[
                        "weight" => [
                            "units" => "KG",
                            "value" => 1
                        ],
                        "trackingNumber" => $trackingNumber
                    ]]
                ]
            ];

            // Log do payload para debug
            Log::info('Payload da requisição FedEx:', ['payload' => $body]);

            // 3. Fazer a requisição para a API da FedEx
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-locale' => 'en_US',
                'Authorization' => 'Bearer ' . $accessToken
            ])->post('https://apis.fedex.com/ship/v1/shipments', $body);

            // Log da resposta para debug
            Log::info('Resposta da FedEx:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'trackingNumber' => $trackingNumber,
                    'labelUrl' => $data['output']['transactionShipments'][0]['pieceResponses'][0]['labelDocuments'][0]['url'],
                    'serviceName' => 'INTERNATIONAL_PRIORITY',
                    'recipient' => [
                        'name' => 'RECIPIENT NAME',
                        'address' => '123 Main St',
                        'city' => 'Miami',
                        'state' => 'FL',
                        'country' => 'US',
                        'postalCode' => '33126'
                    ]
                ]);
            }

            // Tratamento específico para erros da FedEx
            $fedexError = $response->json();
            $errorMessage = 'Erro ao gerar etiqueta na FedEx';
            
            if (isset($fedexError['errors']) && is_array($fedexError['errors'])) {
                foreach ($fedexError['errors'] as $error) {
                    switch ($error['code']) {
                        case 'COMPANYNAME.TOO.LONG':
                            $errorMessage = 'O nome da empresa é muito longo. Por favor, use um nome mais curto.';
                            break;
                        case 'SHIPMENT.ACCOUNTNUMBER.UNAUTHORIZED':
                            $errorMessage = 'Conta FedEx não autorizada. Por favor, verifique as credenciais da conta ou entre em contato com o suporte FedEx.';
                            break;
                        case 'TRACKINGNUMBER.ENTERED.INVALID':
                            $errorMessage = 'Número de rastreamento inválido. Por favor, verifique o número informado.';
                            break;
                        case 'FORBIDDEN.ERROR':
                            $errorMessage = 'Erro de permissão. Por favor, verifique as credenciais e permissões da conta.';
                            break;
                        default:
                            $errorMessage = $error['message'] ?? 'Erro desconhecido ao gerar etiqueta';
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => $fedexError
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Erro ao gerar etiqueta:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar etiqueta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
