<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    protected $baseUrl = 'https://api-sandbox.asaas.com';
    protected $apiToken = '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6Ojk5YWQxY2M2LTg1ODUtNDA2YS04ZWRlLTAzNTY5NDRmYmM2Mjo6JGFhY2hfYTI0ZmIzYjUtMWRiOS00MmJiLWI1MjItYjk1ZWRjNTQxYjI5';

    public function index()
    {
        return view('pagamento.index');
    }
    
    public function armazenarServicoSessao(Request $request)
    {
        try {
            $request->validate([
                'servico' => 'required|string',
                'servicoTipo' => 'required|string',
                'valorTotalBRL' => 'required',
                'tempoEntrega' => 'required|string',
            ]);

            // Converter valorTotalBRL para número se for string
            $valorTotalBRL = $request->valorTotalBRL;
            if (is_string($valorTotalBRL)) {
                // Substituir vírgula por ponto e converter para float
                $valorTotalBRL = (float) str_replace(',', '.', $valorTotalBRL);
            }

            // Armazenar na sessão
            session([
                'servico_selecionado' => [
                    'servico' => $request->servico,
                    'servicoTipo' => $request->servicoTipo,
                    'valorTotalBRL' => $valorTotalBRL,
                    'tempoEntrega' => $request->tempoEntrega,
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Serviço armazenado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao armazenar serviço: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function processar(Request $request)
    {
        try {

            // Obter informações do serviço selecionado
            $servicoInfo = session('servico_selecionado');
              if (!$servicoInfo) {
                  return response()->json([
                      'success' => false,
                      'message' => 'Informações do serviço não encontradas.'
                  ], 400);
            }

            $dadosEnvio = $this->prepararDadosEnvio($request, $servicoInfo);
            /*
            // Validar dados básicos
            $request->validate([
                'payment_method' => 'required|in:credit_card,pix',
                'servico_entrega' => 'required|string',
            ]);
            

            // Obter informações do serviço selecionado
            $servicoInfo = session('servico_selecionado');
            if (!$servicoInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informações do serviço não encontradas.'
                ], 400);
            }

            $valor = $servicoInfo['valorTotalBRL'] ?? 0;
            $paymentMethod = $request->payment_method;

            /*
            // Criar ou obter cliente
            $customerId = $this->criarOuObterCliente($request);

            $pagamentoResponse = null;
            
            if ($paymentMethod === 'credit_card') {
                $pagamentoResponse = $this->processarPagamentoCartao($request, $customerId, $valor, $servicoInfo);
            } else {
                $pagamentoResponse = $this->processarPagamentoPix($request, $customerId, $valor, $servicoInfo);
            }

            
            // Se o pagamento foi processado com sucesso, processar o envio
           // if ($pagamentoResponse && $pagamentoResponse->getData()->success) {
                // Preparar dados para processamento de envio
                $dadosEnvio = $this->prepararDadosEnvio($request, $servicoInfo);
                
                // Fazer requisição interna para processar o envio
                $envioResponse = $this->processarEnvioInterno($dadosEnvio);
                
                // Se o envio foi processado com sucesso, retornar resposta combinada
                if ($envioResponse['success']) {
                  //  $responseData = $pagamentoResponse->getData();
                    $responseData->envio_success = true;
                    $responseData->envio_message = $envioResponse['message'];
                    $responseData->shipment_id = $envioResponse['shipmentId'] ?? null;
                    $responseData->tracking_number = $envioResponse['trackingNumber'] ?? null;
                    
                    return response()->json($responseData);
                } else {
                    // Se o envio falhou, ainda retornar sucesso do pagamento mas com aviso
                    $responseData = $pagamentoResponse->getData();
                    $responseData->envio_success = false;
                    $responseData->envio_message = $envioResponse['message'];
                    
                    return response()->json($responseData);
                }
           // }
*/
            return $dadosEnvio    ;

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    private function criarOuObterCliente(Request $request)
    {
        // Dados do cliente (você pode ajustar conforme necessário)
        $clienteData = [
            'name' => $request->input('destino_nome', 'Cliente Logiez'),
            'email' => $request->input('destino_email', 'cliente@logiez.com'),
            'phone' => $request->input('destino_telefone', '4832999999'),
            'mobilePhone' => $request->input('destino_telefone', '48989999999'),
            'cpfCnpj' => $request->input('destino_cpf', '24971563792'),
            'postalCode' => $request->input('destino_cep', '01310000'),
            'address' => $request->input('destino_endereco', 'Avenida Paulista'),
            'addressNumber' => $request->input('destino_numero', '150'),
            'complement' => $request->input('destino_complemento', 'Sala 10'),
            'province' => $request->input('destino_bairro', 'Centro'),
            'externalReference' => 'cliente-logiez-' . uniqid(),
            'notificationDisabled' => false
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $this->apiToken,
            'User-Agent' => 'MVL Logiez/1.0'
        ])->post($this->baseUrl . '/v3/customers', $clienteData);

        if ($response->successful()) {
            $data = $response->json();
            return $data['id'];
        }

        throw new \Exception('Erro ao criar cliente: ' . $response->body());
    }

    private function processarPagamentoCartao(Request $request, $customerId, $valor, $servicoInfo)
    {
        // Validar campos do cartão
        $request->validate([
            'card_number' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string',
            'card_name' => 'required|string',
            'installments' => 'required|integer|min:1|max:12'
        ]);

        // Criar cobrança
        $cobrancaData = [
            'customer' => $customerId,
            'billingType' => 'CREDIT_CARD',
            'dueDate' => date('Y-m-d'),
            'value' => $valor,
            'description' => 'Envio Logiez - ' . $servicoInfo['servico'],
            'externalReference' => 'envio-logiez-' . uniqid(),
            'remoteIp' => $request->ip()
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $this->apiToken,
            'User-Agent' => 'MVL Logiez/1.0'
        ])->post($this->baseUrl . '/v3/payments', $cobrancaData);

        if (!$response->successful()) {
            throw new \Exception('Erro ao criar cobrança: ' . $response->body());
        }

        $cobranca = $response->json();
        $cobrancaId = $cobranca['id'];

        // Processar pagamento com cartão
        $cartaoData = [
            'customer' => $customerId,
            'billingType' => 'CREDIT_CARD',
            'creditCard' => [
                'holderName' => $request->card_name,
                'number' => str_replace(' ', '', $request->card_number),
                'expiryMonth' => explode('/', $request->card_expiry)[0],
                'expiryYear' => '20' . explode('/', $request->card_expiry)[1],
                'ccv' => $request->card_cvv
            ],
            'creditCardHolderInfo' => [
                'name' => $request->card_name,
                'email' => $request->input('destino_email', 'cliente@logiez.com'),
                'cpfCnpj' => $request->input('destino_cpf', '24971563792'),
                'postalCode' => $request->input('destino_cep', '01310000'),
                'addressNumber' => $request->input('destino_numero', '150'),
                'addressComplement' => $request->input('destino_complemento', 'Sala 10'),
                'phone' => $request->input('destino_telefone', '4832999999'),
                'mobilePhone' => $request->input('destino_telefone', '48989999999')
            ],
            'remoteIp' => $request->ip()
        ];

        $pagamentoResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $this->apiToken,
            'User-Agent' => 'MVL Logiez/1.0'
        ])->post($this->baseUrl . '/v3/payments/' . $cobrancaId . '/payWithCreditCard', $cartaoData);

        if ($pagamentoResponse->successful()) {
            $pagamento = $pagamentoResponse->json();
            
            return response()->json([
                'success' => true,
                'payment_id' => $pagamento['id'],
                'status' => $pagamento['status'],
                'valor' => $valor,
                'message' => 'Pagamento processado com sucesso!'
            ]);
        } else {
            throw new \Exception('Erro ao processar pagamento com cartão: ' . $pagamentoResponse->body());
        }
    }

    private function processarPagamentoPix(Request $request, $customerId, $valor, $servicoInfo)
    {
        // Criar cobrança PIX
        $pixData = [
            'customer' => $customerId,
            'billingType' => 'PIX',
            'dueDate' => date('Y-m-d', strtotime('+3 days')),
            'value' => $valor,
            'description' => 'Envio Logiez - ' . $servicoInfo['servico'],
            'externalReference' => 'pix-logiez-' . uniqid()
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $this->apiToken,
            'User-Agent' => 'MVL Logiez/1.0'
        ])->post($this->baseUrl . '/v3/payments', $pixData);

        if (!$response->successful()) {
            throw new \Exception('Erro ao criar cobrança PIX: ' . $response->body());
        }

        $pix = $response->json();
        $pixId = $pix['id'];

        // Obter QR Code PIX
        $qrCodeResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $this->apiToken,
            'User-Agent' => 'MVL Logiez/1.0'
        ])->get($this->baseUrl . '/v3/payments/' . $pixId . '/pixQrCode');

        if ($qrCodeResponse->successful()) {
            $qrCode = $qrCodeResponse->json();
            
            return response()->json([
                'success' => true,
                'payment_id' => $pixId,
                'status' => $pix['status'],
                'valor' => $valor,
                'qr_code_url' => $qrCode['payload'] ?? '',
                'qr_code_expiration' => $qrCode['expirationDate'] ?? '',
                'message' => 'QR Code PIX gerado com sucesso!'
            ]);
        } else {
            throw new \Exception('Erro ao gerar QR Code PIX: ' . $qrCodeResponse->body());
        }
    }

    public function sucesso(Request $request)
    {
        $paymentId = $request->get('id');
        
        return view('pagamento.sucesso', [
            'payment_id' => $paymentId
        ]);
    }

    public function verificarStatus($id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'access_token' => $this->apiToken,
                'User-Agent' => 'MVL Logiez/1.0'
            ])->get($this->baseUrl . '/v3/payments/' . $id);

            if ($response->successful()) {
                $payment = $response->json();
                
                return response()->json([
                    'success' => true,
                    'status' => $payment['status'],
                    'value' => $payment['value'],
                    'dueDate' => $payment['dueDate']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao verificar status do pagamento'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepara os dados do formulário de envio para processamento
     */
    private function prepararDadosEnvio(Request $request, $servicoInfo)
    {
        // Obter dados da sessão se disponíveis
        $dadosEnvio = [
            // Dados do serviço
            'servico_entrega' => $servicoInfo['servicoTipo'],
            
            // Dados de origem (do formulário de envio)
            'origem_nome' => $request->input('origem_nome'),
            'origem_endereco' => $request->input('origem_endereco'),
            'origem_complemento' => $request->input('origem_complemento'),
            'origem_cidade' => $request->input('origem_cidade'),
            'origem_estado' => $request->input('origem_estado'),
            'origem_cep' => $request->input('origem_cep'),
            'origem_pais' => $request->input('origem_pais'),
            'origem_telefone' => $request->input('origem_telefone'),
            'origem_email' => $request->input('origem_email'),
            
            // Dados de destino (do formulário de envio)
            'destino_nome' => $request->input('destino_nome'),
            'destino_endereco' => $request->input('destino_endereco'),
            'destino_complemento' => $request->input('destino_complemento'),
            'destino_cidade' => $request->input('destino_cidade'),
            'destino_estado' => $request->input('destino_estado'),
            'destino_cep' => $request->input('destino_cep'),
            'destino_pais' => $request->input('destino_pais'),
            'destino_telefone' => $request->input('destino_telefone'),
            'destino_email' => $request->input('destino_email'),
            
            // Dados da caixa
            'altura' => $request->input('altura'),
            'largura' => $request->input('largura'),
            'comprimento' => $request->input('comprimento'),
            'peso_caixa' => $request->input('peso_caixa'),
            
            // Dados dos produtos
            'produtos_json' => $request->input('produtos_json'),
            'valor_total' => $request->input('valor_total'),
            'peso_total' => $request->input('peso_total'),
            
            // Dados adicionais
            'tipo_envio' => $request->input('tipo_envio'),
            'tipo_pessoa' => $request->input('tipo_pessoa'),
            'tipo_operacao' => $request->input('tipo_operacao'),
            'cpf' => $request->input('cpf'),
            'cnpj' => $request->input('cnpj'),
            
            // Token CSRF
            '_token' => $request->input('_token'),
        ];

        // Log para debug
        Log::info('Dados de envio preparados:', $dadosEnvio);

        return $dadosEnvio;
    }

    /**
     * Faz requisição interna para processar o envio
     */
    private function processarEnvioInterno($dadosEnvio)
    {
        try {
            // Log para debug
            Log::info('Iniciando processamento interno de envio');
            
            // Criar uma nova requisição para o processamento de envio
            $envioRequest = new \Illuminate\Http\Request();
            $envioRequest->merge($dadosEnvio);
            
            // Instanciar o SectionController usando o container do Laravel
            $sectionController = app(\App\Http\Controllers\SectionController::class);
            
            $response = $sectionController->processarEnvio($envioRequest);
            
            // Log para debug
            Log::info('Resposta do processamento de envio:', ['response' => $response]);
            
            // Se a resposta for um JSON response, extrair os dados
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);
                Log::info('Dados extraídos da resposta:', $responseData);
                return $responseData;
            }
            
            return [
                'success' => true,
                'message' => 'Envio processado com sucesso'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar envio interno:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Erro ao processar envio: ' . $e->getMessage()
            ];
        }
    }
} 