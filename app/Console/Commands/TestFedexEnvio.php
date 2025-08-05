<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Shipment;
use App\Models\SenderAddress;
use App\Models\RecipientAddress;
use App\Models\ShipmentItem;
use App\Models\ApiLog;
use Illuminate\Support\Facades\DB;

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
            'nome' => 'Pedro Afonso da Costa Penha',
            'endereco' => 'Rua Gioconda Mora',
            'complemento' => 'Sala 123',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => $this->validarCodigoPostal('01310200', 'BR'), // CEP válido de São Paulo
            'pais' => 'BR',
            'telefone' => '35999028971',
            'email' => 'pedro.teste@gmail.com'
        ];
        
        $dadosDestinatario = [
            'nome' => 'Rui Vergani Neto',
            'endereco' => 'Celebration',
            'complemento' => 'Suite 456',
            'cidade' => 'Celebration',
            'estado' => 'FL',
            'cep' => $this->validarCodigoPostal('34747', 'US'), // ZIP válido de Celebration, FL
            'pais' => 'US',
            'telefone' => '3055551234',
            'email' => 'rui.teste@gmail.com'
        ];
        
        $dadosPacote = [
            'altura' => 20,            
            'largura' => 10,           
            'comprimento' => 20,       
            'peso' => 5               
        ];
        
        $dadosProdutos = [
            [
                'descricao' => 'Notebook',
                'peso' => 5,
                'quantidade' => 1,
                'valor_unitario' => 1590.00,
                'pais_origem' => 'BR',
                'ncm' => '84713019'   
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
            // Usar as credenciais de produção configuradas no sistema
            $apiUrl = config('services.fedex.api_url');
            $clientId = config('services.fedex.client_id');
            $clientSecret = config('services.fedex.client_secret');
            $shipperAccount = config('services.fedex.shipper_account');
            
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
            
            // SALVAR INFORMAÇÕES NO BANCO DE DADOS
            $this->info('💾 Salvando informações no banco de dados...');
            
            try {
                DB::beginTransaction();
                
                // 1. Criar o registro principal do envio
                $shipment = Shipment::create([
                    'tracking_number' => $trackingNumber,
                    'shipment_id' => $shipmentId,
                    'carrier' => 'FEDEX',
                    'tipo_envio' => 'venda', // Valor padrão para teste
                    'tipo_pessoa' => 'pf', // Valor padrão para teste
                    'service_code' => $servicoEntrega,
                    'service_name' => $servicoEntrega,
                    'label_url' => $labelUrl,
                    'label_format' => 'PDF',
                    'status' => 'created',
                    'status_description' => 'Envio criado com sucesso',
                    'last_status_update' => now(),
                    'package_height' => $dadosPacote['altura'],
                    'package_width' => $dadosPacote['largura'],
                    'package_length' => $dadosPacote['comprimento'],
                    'package_weight' => $dadosPacote['peso'],
                    'currency' => 'USD',
                    'ship_date' => $shipDate,
                    'is_simulation' => false,
                    'was_delivered' => false,
                    'has_issues' => false,
                ]);
                
                // 2. Criar endereço do remetente
                SenderAddress::create([
                    'shipment_id' => $shipment->id,
                    'name' => $dadosRemetente['nome'],
                    'phone' => $dadosRemetente['telefone'],
                    'email' => $dadosRemetente['email'],
                    'address' => $dadosRemetente['endereco'],
                    'address_complement' => $dadosRemetente['complemento'],
                    'city' => $dadosRemetente['cidade'],
                    'state' => $dadosRemetente['estado'],
                    'postal_code' => $dadosRemetente['cep'],
                    'country' => $dadosRemetente['pais'],
                    'is_residential' => false,
                ]);
                
                // 3. Criar endereço do destinatário
                RecipientAddress::create([
                    'shipment_id' => $shipment->id,
                    'name' => $dadosDestinatario['nome'],
                    'phone' => $dadosDestinatario['telefone'],
                    'email' => $dadosDestinatario['email'],
                    'address' => $dadosDestinatario['endereco'],
                    'address_complement' => $dadosDestinatario['complemento'],
                    'city' => $dadosDestinatario['cidade'],
                    'state' => $dadosDestinatario['estado'],
                    'postal_code' => $dadosDestinatario['cep'],
                    'country' => $dadosDestinatario['pais'],
                    'is_residential' => false,
                ]);
                
                // 4. Criar itens do envio
                foreach ($dadosProdutos as $produto) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'description' => $produto['descricao'],
                        'weight' => $produto['peso'],
                        'quantity' => $produto['quantidade'],
                        'unit_price' => $produto['valor_unitario'],
                        'total_price' => $produto['valor_unitario'] * $produto['quantidade'],
                        'currency' => 'USD',
                        'country_of_origin' => $produto['pais_origem'],
                        'harmonized_code' => $produto['ncm'],
                    ]);
                }
                
                // 5. Registrar log da API
                ApiLog::create([
                    'api_service' => 'FEDEX',
                    'endpoint' => $shipUrl,
                    'http_method' => 'POST',
                    'request_data' => json_encode($shipRequest),
                    'response_data' => json_encode($shipData),
                    'response_code' => $shipHttpCode,
                    'status' => 'success',
                    'created_at' => now(),
                ]);
                
                DB::commit();
                
                $this->info('✅ Dados salvos no banco com sucesso!');
                $this->info("ID do envio no banco: {$shipment->id}");
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('❌ Erro ao salvar no banco: ' . $e->getMessage());
                Log::error('Erro ao salvar envio no banco: ' . $e->getMessage(), [
                    'tracking_number' => $trackingNumber,
                    'shipment_id' => $shipmentId,
                    'error' => $e->getMessage()
                ]);
            }
            
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
            
            return Command::FAILURE;
        }
    }

    private function validarCodigoPostal($postalCode, $countryCode)
    {
        // Remover caracteres não alfanuméricos
        $postalCode = preg_replace('/[^A-Za-z0-9]/', '', $postalCode);
        
        // Converter para maiúsculas
        $postalCode = strtoupper($postalCode);
        
        switch ($countryCode) {
            case 'BR':
                // CEP brasileiro: 8 dígitos numéricos
                if (strlen($postalCode) === 8 && ctype_digit($postalCode)) {
                    return $postalCode;
                }
                // Se não estiver no formato correto, retornar um CEP válido de exemplo
                return '01310200'; // CEP válido de São Paulo
                
            case 'US':
                // ZIP code americano: 5 dígitos ou 5+4 dígitos
                if (strlen($postalCode) === 5 && ctype_digit($postalCode)) {
                    return $postalCode;
                }
                if (strlen($postalCode) === 9 && ctype_digit($postalCode)) {
                    return substr($postalCode, 0, 5) . '-' . substr($postalCode, 5);
                }
                // Se não estiver no formato correto, retornar um ZIP válido de exemplo
                return '10001'; // ZIP válido de Nova York
                
            case 'CA':
                // Código postal canadense: A1A 1A1 (letra, dígito, letra, espaço, dígito, letra, dígito)
                if (preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/', $postalCode)) {
                    return str_replace(' ', '', $postalCode);
                }
                return 'M5V3A8'; // Código postal válido de Toronto
                
            case 'MX':
                // Código postal mexicano: 5 dígitos
                if (strlen($postalCode) === 5 && ctype_digit($postalCode)) {
                    return $postalCode;
                }
                return '06000'; // Código postal válido da Cidade do México
                
            case 'AR':
                // Código postal argentino: 4 dígitos
                if (strlen($postalCode) === 4 && ctype_digit($postalCode)) {
                    return $postalCode;
                }
                return '1001'; // Código postal válido de Buenos Aires
                
            default:
                // Para outros países, retornar o código limpo ou um valor padrão
                if (strlen($postalCode) >= 3 && strlen($postalCode) <= 10) {
                    return $postalCode;
                }
                return '00000';
        }
    }
} 