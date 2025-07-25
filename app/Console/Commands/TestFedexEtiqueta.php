<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Shipment;
use App\Models\SenderAddress;
use App\Models\RecipientAddress;
use App\Models\ShipmentItem;

class TestFedexEtiqueta extends Command
{
    protected $signature = 'fedex:test-etiqueta {codigo}';
    protected $description = 'Testa a API interna /api/fedex/etiqueta enviando um código de rastreamento';

    public function handle()
    {
        $trackingNumber = $this->argument('codigo');
        
        // Buscar o envio pelo código
        $shipment = Shipment::with(['senderAddress', 'recipientAddress', 'items'])
            ->where('tracking_number', $trackingNumber)
            ->first();

        // 1. Autenticação na FedEx
        $auth = Http::asForm()->post(config('services.fedex.api_url') . '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.fedex.client_id'),
            'client_secret' => config('services.fedex.client_secret'),
        ]);

        $accessToken = $auth->json()['access_token'] ?? null;
        if (!$accessToken) {
            $this->error('Erro ao autenticar na FedEx');
            return 1;
        }

        // 2. Montar o corpo da requisição
        if ($shipment) {
            $this->info('Usando dados do banco de dados para o envio ' . $trackingNumber);
            $body = [
                "labelResponseOptions" => "URL_ONLY",
                "accountNumber" => ["value" => config('services.fedex.account_number')],
                "requestedShipment" => [
                    "shipDatestamp" => $shipment->ship_date->format('Y-m-d'),
                    "serviceType" => $shipment->service_code,
                    "packagingType" => "YOUR_PACKAGING",
                    "pickupType" => "USE_SCHEDULED_PICKUP",
                    "blockInsightVisibility" => false,
                    "shipper" => [
                        "contact" => [
                            "personName" => $shipment->senderAddress->name,
                            "phoneNumber" => preg_replace('/\D/', '', $shipment->senderAddress->phone),
                            "companyName" => "LS COMÉRCIO ATACADISTA E VAREJISTA LTDA"
                        ],
                        "address" => [
                            "streetLines" => [
                                $shipment->senderAddress->address,
                                $shipment->senderAddress->address_complement
                            ],
                            "city" => $shipment->senderAddress->city,
                            "stateOrProvinceCode" => $shipment->senderAddress->state,
                            "postalCode" => $shipment->senderAddress->postal_code,
                            "countryCode" => $shipment->senderAddress->country
                        ]
                    ],
                    "recipients" => [[
                        "contact" => [
                            "personName" => $shipment->recipientAddress->name,
                            "phoneNumber" => preg_replace('/\D/', '', $shipment->recipientAddress->phone),
                            "companyName" => $shipment->recipientAddress->name
                        ],
                        "address" => [
                            "streetLines" => [
                                $shipment->recipientAddress->address,
                                $shipment->recipientAddress->address_complement
                            ],
                            "city" => $shipment->recipientAddress->city,
                            "stateOrProvinceCode" => $shipment->recipientAddress->state,
                            "postalCode" => $shipment->recipientAddress->postal_code,
                            "countryCode" => $shipment->recipientAddress->country,
                            "residential" => $shipment->recipientAddress->is_residential
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
                        "commodities" => $shipment->items->map(function($item) {
                            return [
                                "description" => $item->description,
                                "countryOfManufacture" => $item->country_of_origin ?? "BR",
                                "harmonizedCode" => $item->harmonized_code,
                                "quantity" => $item->quantity,
                                "quantityUnits" => "PCS",
                                "unitPrice" => [
                                    "amount" => $item->unit_price,
                                    "currency" => $item->currency
                                ],
                                "customsValue" => [
                                    "amount" => $item->total_price,
                                    "currency" => $item->currency
                                ],
                                "weight" => [
                                    "units" => "KG",
                                    "value" => $item->weight
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
                            "value" => $shipment->package_weight
                        ]
                    ]]
                ]
            ];
        } else {
            $this->info('Envio não encontrado no banco. Usando apenas o tracking number ' . $trackingNumber);
            // Payload mínimo necessário quando não temos dados do banco
            $body = [
                "labelResponseOptions" => "URL_ONLY",
                "accountNumber" => ["value" => config('services.fedex.account_number')],
                "requestedShipment" => [
                    "shipDatestamp" => now()->format('Y-m-d'),
                    "serviceType" => "INTERNATIONAL_PRIORITY",
                    "packagingType" => "YOUR_PACKAGING",
                    "pickupType" => "USE_SCHEDULED_PICKUP",
                    "blockInsightVisibility" => false,
                    "shipper" => [
                        "contact" => [
                            "personName" => "LS COMÉRCIO",
                            "phoneNumber" => "19981166445",
                            "companyName" => "LS COMÉRCIO ATACADISTA E VAREJISTA LTDA"
                        ],
                        "address" => [
                            "streetLines" => [
                                "Rua 4, Pq Res. Dona Chiquinha"
                            ],
                            "city" => "Cosmópolis",
                            "stateOrProvinceCode" => "SP",
                            "postalCode" => "13150000",
                            "countryCode" => "BR"
                        ]
                    ],
                    "recipients" => [[
                        "contact" => [
                            "personName" => "RECIPIENT NAME",
                            "phoneNumber" => "1234567890",
                            "companyName" => "Recipient Company"
                        ],
                        "address" => [
                            "streetLines" => [
                                "123 Main St"
                            ],
                            "city" => "Miami",
                            "stateOrProvinceCode" => "FL",
                            "postalCode" => "33126",
                            "countryCode" => "US",
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
                        "dutiesPayment" => ["paymentType" => "RECIPIENT"],
                        "isDocumentOnly" => false,
                        "commodities" => [[
                            "description" => "Sample Product",
                            "countryOfManufacture" => "BR",
                            "harmonizedCode" => "000000",
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
                    "requestedPackageLineItems" => [[
                        "weight" => [
                            "units" => "KG",
                            "value" => 1
                        ]
                    ]]
                ]
            ];
        }

        // Validar tamanho mínimo das cidades
        $shipperCity = $body['requestedShipment']['shipper']['address']['city'];
        $recipientCity = $body['requestedShipment']['recipients'][0]['address']['city'];

        if (strlen($shipperCity) < 3) {
            $this->error('A cidade do remetente deve ter pelo menos 3 caracteres.');
            return 1;
        }

        if (strlen($recipientCity) < 3) {
            $this->error('A cidade do destinatário deve ter pelo menos 3 caracteres.');
            return 1;
        }

        // 3. Fazer a requisição para a API da FedEx
        $response = Http::withToken($accessToken)
            ->post(config('services.fedex.api_url') . config('services.fedex.ship_endpoint', '/ship/v1/shipments'), $body);

        $this->info('Status: ' . $response->status());
        $this->line('Resposta:');
        $this->line($response->body());
        
        return 0;
    }
} 