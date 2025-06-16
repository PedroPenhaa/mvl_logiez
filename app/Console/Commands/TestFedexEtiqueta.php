<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestFedexEtiqueta extends Command
{
    protected $signature = 'fedex:test-etiqueta {codigo}';
    protected $description = 'Testa a API interna /api/fedex/etiqueta enviando um código de rastreamento';

    public function handle()
    {
        // 1. Autenticação na FedEx
        $auth = Http::asForm()->post('https://apis-sandbox.fedex.com/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => 'l7517499d73dc1470c8f56fe055c45113c',
            'client_secret' => '41d8172c88c345cca8f47695bc97a5cd',
        ]);
        
        $accessToken = $auth->json()['access_token'] ?? null;
        if (!$accessToken) {
            $this->error('Erro ao autenticar na FedEx');
            return 1;
        }

        // 2. Montar o corpo da requisição
        $body = [
            "labelResponseOptions" => "URL_ONLY",
            "accountNumber" => ["value" => "740561073"],
            "requestedShipment" => [
                "shipDatestamp" => "2024-10-01",
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
                        "value" => "Cust_Ref1"
                    ]],
                    "weight" => ["units" => "KG", "value" => 0.01]
                ]],
                "rateRequestType" => ["LIST", "PREFERRED"]
            ]
        ];

        // 3. Fazer a requisição para a API da FedEx
        $response = Http::withToken($accessToken)
            ->post('https://apis-sandbox.fedex.com/ship/v1/shipments', $body);

        $this->info('Status: ' . $response->status());
        $this->line('Resposta:');
        $this->line($response->body());
        
        return 0;
    }
} 