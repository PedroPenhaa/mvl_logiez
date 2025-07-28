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

            // Criar ou obter cliente
            $customerId = $this->criarOuObterCliente($request);

            if ($paymentMethod === 'credit_card') {
                return $this->processarPagamentoCartao($request, $customerId, $valor, $servicoInfo);
            } else {
                return $this->processarPagamentoPix($request, $customerId, $valor, $servicoInfo);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
} 