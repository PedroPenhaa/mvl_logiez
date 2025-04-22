<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestFedexEnvio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fedex:test-envio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a API de envio da FedEx';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚚 Testando criação de envio FedEx');
        $this->info('----------------------------------');

        // Dados de teste para o envio
        $dadosRemetente = [
            'nome' => 'João Silva',
            'endereco' => 'Avenida Paulista, 1000',
            'complemento' => 'Sala 123',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01310100',
            'pais' => 'BR',
            'telefone' => '11999998888',
            'email' => 'teste@exemplo.com'
        ];
        
        $dadosDestinatario = [
            'nome' => 'John Doe',
            'endereco' => '123 Ocean Drive',
            'complemento' => 'Suite 456',
            'cidade' => 'Miami',
            'estado' => 'FL',
            'cep' => '33131',
            'pais' => 'US',
            'telefone' => '13058889999',
            'email' => 'johndoe@example.com'
        ];
        
        $dadosPacote = [
            'altura' => 10,            // 10 cm
            'largura' => 20,           // 20 cm
            'comprimento' => 30,       // 30 cm
            'peso' => 5                // 5 kg
        ];
        
        $dadosProdutos = [
            [
                'descricao' => 'Produto de Teste 1',
                'peso' => 2,
                'quantidade' => 1,
                'valor_unitario' => 100.00,
                'pais_origem' => 'BR',
                'ncm' => '85171231'    // NCM para smartphones
            ],
            [
                'descricao' => 'Produto de Teste 2',
                'peso' => 3,
                'quantidade' => 1,
                'valor_unitario' => 50.00,
                'pais_origem' => 'BR',
                'ncm' => '42021220'    // NCM para bolsas
            ]
        ];
        
        $servicoEntrega = 'FEDEX_INTERNATIONAL_PRIORITY';

        $this->info("📦 Dados do Envio:");
        $this->info("Remetente: {$dadosRemetente['nome']} ({$dadosRemetente['cidade']}, {$dadosRemetente['pais']})");
        $this->info("Destinatário: {$dadosDestinatario['nome']} ({$dadosDestinatario['cidade']}, {$dadosDestinatario['pais']})");
        $this->info("Dimensões: {$dadosPacote['altura']}cm x {$dadosPacote['largura']}cm x {$dadosPacote['comprimento']}cm");
        $this->info("Peso: {$dadosPacote['peso']}kg");
        $this->info("Serviço: $servicoEntrega");
        $this->info("Produtos: " . count($dadosProdutos));

        $this->newLine();
        $this->info('⏳ Enviando requisição para criar envio...');
        
        try {
            // Usar as credenciais que funcionaram na cotação
            $apiUrl = "https://apis-sandbox.fedex.com";
            $clientId = "l774cb4034be8346eb9e3a476a633764e3";
            $clientSecret = "1e939e0d9e8747eb92319e28970b5bc0";
            $shipperAccount = "740561073";
            
            // Obter token de autenticação
            $authUrl = $apiUrl . '/oauth/token';
            
            $tokenPayload = [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret
            ];
            
            $this->info('Obtendo token de autenticação...');
            
            $authCurl = curl_init();
            curl_setopt_array($authCurl, [
                CURLOPT_URL => $authUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($tokenPayload),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/x-www-form-urlencoded",
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_VERBOSE => true
            ]);
            
            $authResponse = curl_exec($authCurl);
            $authHttpCode = curl_getinfo($authCurl, CURLINFO_HTTP_CODE);
            $authErr = curl_error($authCurl);
            
            curl_close($authCurl);
            
            if ($authErr) {
                throw new \Exception('Erro na autenticação: ' . $authErr);
            }
            
            if ($authHttpCode != 200) {
                $errorDetails = json_decode($authResponse, true);
                $errorMessage = 'Resposta da API de autenticação: ' . substr($authResponse, 0, 500);
                throw new \Exception('Falha na autenticação. Código HTTP: ' . $authHttpCode . "\n" . $errorMessage);
            }
            
            $authData = json_decode($authResponse, true);
            $accessToken = $authData['access_token'] ?? null;
            
            if (!$accessToken) {
                throw new \Exception('Não foi possível obter o token de acesso.');
            }
            
            $this->info('✅ Token de autenticação obtido com sucesso!');
            $this->info('Preparando dados para envio...');
            
            // Preparar requisição de criação de envio
            $shipUrl = $apiUrl . '/ship/v1/shipments';
            $transactionId = uniqid('logiez_ship_');
            $shipDate = date('Y-m-d');
            
            // Montar commodities para a alfândega
            $commodities = [];
            foreach ($dadosProdutos as $produto) {
                $commodities[] = [
                    'description' => $produto['descricao'],
                    'weight' => [
                        'units' => 'KG',
                        'value' => $produto['peso']
                    ],
                    'quantity' => $produto['quantidade'],
                    'quantityUnits' => 'PCS',
                    'unitPrice' => [
                        'amount' => $produto['valor_unitario'],
                        'currency' => 'USD'
                    ],
                    'customsValue' => [
                        'amount' => $produto['valor_unitario'] * $produto['quantidade'],
                        'currency' => 'USD'
                    ],
                    'countryOfManufacture' => $produto['pais_origem'],
                    'harmonizedCode' => $produto['ncm']
                ];
            }
            
            // Construir payload da requisição
            $shipRequest = [
                'labelResponseOptions' => 'URL_ONLY',
                'requestedShipment' => [
                    'shipper' => [
                        'contact' => [
                            'personName' => $dadosRemetente['nome'],
                            'phoneNumber' => $dadosRemetente['telefone'],
                            'emailAddress' => $dadosRemetente['email']
                        ],
                        'address' => [
                            'streetLines' => [
                                $dadosRemetente['endereco'],
                                $dadosRemetente['complemento'] ?? ''
                            ],
                            'city' => $dadosRemetente['cidade'],
                            'stateOrProvinceCode' => $dadosRemetente['estado'],
                            'postalCode' => $dadosRemetente['cep'],
                            'countryCode' => $dadosRemetente['pais'],
                            'residential' => false
                        ]
                    ],
                    'recipients' => [
                        [
                            'contact' => [
                                'personName' => $dadosDestinatario['nome'],
                                'phoneNumber' => $dadosDestinatario['telefone'],
                                'emailAddress' => $dadosDestinatario['email']
                            ],
                            'address' => [
                                'streetLines' => [
                                    $dadosDestinatario['endereco'],
                                    $dadosDestinatario['complemento'] ?? ''
                                ],
                                'city' => $dadosDestinatario['cidade'],
                                'stateOrProvinceCode' => $dadosDestinatario['estado'],
                                'postalCode' => $dadosDestinatario['cep'],
                                'countryCode' => $dadosDestinatario['pais'],
                                'residential' => false
                            ]
                        ]
                    ],
                    'shipDatestamp' => $shipDate,
                    'serviceType' => $servicoEntrega,
                    'packagingType' => 'YOUR_PACKAGING',
                    'pickupType' => 'USE_SCHEDULED_PICKUP',
                    'blockInsightVisibility' => false,
                    'shippingChargesPayment' => [
                        'paymentType' => 'SENDER',
                        'payor' => [
                            'responsibleParty' => [
                                'accountNumber' => [
                                    'value' => $shipperAccount
                                ]
                            ]
                        ]
                    ],
                    'labelSpecification' => [
                        'labelFormatType' => 'COMMON2D',
                        'imageType' => 'PDF',
                        'labelStockType' => 'PAPER_85X11_TOP_HALF_LABEL'
                    ],
                    'rateRequestType' => ['ACCOUNT', 'LIST'],
                    'preferredCurrency' => 'USD',
                    'totalPackageCount' => 1,
                    'requestedPackageLineItems' => [
                        [
                            'weight' => [
                                'units' => 'KG',
                                'value' => $dadosPacote['peso']
                            ],
                            'dimensions' => [
                                'length' => $dadosPacote['comprimento'],
                                'width' => $dadosPacote['largura'],
                                'height' => $dadosPacote['altura'],
                                'units' => 'CM'
                            ],
                            'groupPackageCount' => 1
                        ]
                    ],
                    'customsClearanceDetail' => [
                        'dutiesPayment' => [
                            'paymentType' => 'SENDER',
                            'payor' => [
                                'responsibleParty' => [
                                    'accountNumber' => [
                                        'value' => $shipperAccount
                                    ]
                                ]
                            ]
                        ],
                        'commodities' => $commodities,
                        'commercialInvoice' => [
                            'purpose' => 'GIFT'
                        ]
                    ]
                ],
                'accountNumber' => [
                    'value' => $shipperAccount
                ]
            ];
            
            $this->info('Enviando requisição para API de envio...');
            
            // Fazer a requisição
            $shipCurl = curl_init();
            curl_setopt_array($shipCurl, [
                CURLOPT_URL => $shipUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($shipRequest),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: Bearer " . $accessToken,
                    "X-locale: en_US",
                    "x-customer-transaction-id: " . $transactionId
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_VERBOSE => true
            ]);
            
            $shipResponse = curl_exec($shipCurl);
            $shipHttpCode = curl_getinfo($shipCurl, CURLINFO_HTTP_CODE);
            $shipErr = curl_error($shipCurl);
            
            curl_close($shipCurl);
            
            // Log da resposta detalhada para debug
            $this->info('Resposta da API - Status HTTP: ' . $shipHttpCode);
            if ($shipErr) {
                $this->error('Erro cURL: ' . $shipErr);
            }
            
            // Se houver erro, disparar exceção
            if ($shipErr) {
                throw new \Exception('Erro na requisição de envio: ' . $shipErr);
            }
            
            if ($shipHttpCode != 200) {
                $errorDetails = json_decode($shipResponse, true);
                $errorMessage = 'Resposta da API: ' . substr($shipResponse, 0, 500);
                throw new \Exception('Falha no envio. Código HTTP: ' . $shipHttpCode . "\n" . $errorMessage);
            }
            
            // Processar resposta
            $shipData = json_decode($shipResponse, true);
            
            // Extrair informações relevantes
            $trackingNumber = $shipData['output']['transactionShipments'][0]['masterTrackingNumber'] ?? null;
            $shipmentId = $shipData['output']['transactionShipments'][0]['shipmentDocuments'][0]['shipmentId'] ?? null;
            $labelUrl = $shipData['output']['transactionShipments'][0]['shipmentDocuments'][0]['url'] ?? null;
            
            $resultado = [
                'success' => true,
                'trackingNumber' => $trackingNumber,
                'shipmentId' => $shipmentId,
                'labelUrl' => $labelUrl,
                'servicoContratado' => $servicoEntrega,
                'dataCriacao' => date('Y-m-d H:i:s'),
                'simulado' => false
            ];
            
            // Imprimir resultado do envio
            $this->newLine();
            $this->info('✅ Envio criado com sucesso!');
            $this->info('----------------------------------');
            $this->info("Número de Rastreio: {$resultado['trackingNumber']}");
            $this->info("ID do Envio: {$resultado['shipmentId']}");
            $this->info("Serviço: {$resultado['servicoContratado']}");
            $this->info("Data de Criação: {$resultado['dataCriacao']}");
            
            if ($labelUrl) {
                $this->info("URL da Etiqueta: {$resultado['labelUrl']}");
                $this->info("Você pode baixar a etiqueta acessando a URL acima.");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erro ao criar envio: ' . $e->getMessage());
            
            Log::error('Erro no command TestFedexEnvio', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
} 