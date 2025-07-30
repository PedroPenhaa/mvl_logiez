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

            // 2. Montar o corpo da requisição para a etiqueta
            $body = [
                "labelResponseOptions" => "URL_ONLY",
                "accountNumber" => [
                    "value" => "207227690"
                ],
                "requestedShipment" => [
                    "shipDatestamp" => "2025-07-25",
                    "serviceType" => "INTERNATIONAL_ECONOMY",
                    "packagingType" => "YOUR_PACKAGING",
                    "pickupType" => "USE_SCHEDULED_PICKUP",
                    "shippingChargesPayment" => [
                        "paymentType" => "SENDER"
                    ],
                    "labelSpecification" => [
                        "imageType" => "PDF",
                        "labelStockType" => "PAPER_85X11_TOP_HALF_LABEL"
                    ],
                    "shipper" => [
                        "contact" => [
                            "personName" => "ISMA",
                            "phoneNumber" => "3219451819"
                        ],
                        "address" => [
                            "streetLines" => ["1480 Celebration Blvd #1051"],
                            "city" => "Kissimmee",
                            "stateOrProvinceCode" => "FL",
                            "postalCode" => "34747",
                            "countryCode" => "US"
                        ]
                    ],
                    "recipients" => [[
                        "contact" => [
                            "personName" => "Alinne Oliveira",
                            "phoneNumber" => "16991442334"
                        ],
                        "address" => [
                            "streetLines" => ["Rua Luis Carvalho Pereira", "504 loja"],
                            "city" => "Ribeirao Preto",
                            "stateOrProvinceCode" => "SP",
                            "postalCode" => "14071",
                            "countryCode" => "BR",
                            "residential" => true
                        ]
                    ]],
                    "customsClearanceDetail" => [
                        "dutiesPayment" => [
                            "paymentType" => "SENDER"
                        ],
                        "commodities" => [[
                            "description" => "Mens polo shirts with short sleeves - dryfit fabric breathable, Mens polo shirts with buttons, Basic short sleeve t-shirts, Collar shirts with mesh or knit panels",
                            "countryOfManufacture" => "BR",
                            "quantity" => 1,
                            "quantityUnits" => "PCS",
                            "unitPrice" => [
                                "amount" => 109.49,
                                "currency" => "USD"
                            ],
                            "customsValue" => [
                                "amount" => 109.49,
                                "currency" => "USD"
                            ],
                            "weight" => [
                                "units" => "KG",
                                "value" => 2.80
                            ]
                        ]]
                    ],
                    "requestedPackageLineItems" => [[
                        "weight" => [
                            "units" => "KG",
                            "value" => 2.80
                        ],
                        "dimensions" => [
                            "length" => 14,
                            "width" => 11,
                            "height" => 8,
                            "units" => "CM"
                        ]
                    ]]
                ]
            ];
            
            // 3. Fazer a requisição para a API da FedEx
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-locale' => 'en_US',
                'Authorization' => 'Bearer ' . $accessToken
            ])->post('https://apis.fedex.com/ship/v1/shipments', $body);

            if ($response->successful()) {
                $data = $response->json();
                $shipment = $data['output']['transactionShipments'][0];
                $piece = $shipment['pieceResponses'][0];
                
                return response()->json([
                    'success' => true,
                    'trackingNumber' => $piece['trackingNumber'],
                    'labelUrl' => $piece['packageDocuments'][0]['url'],
                    'serviceName' => $shipment['serviceName'],
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
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar etiqueta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
