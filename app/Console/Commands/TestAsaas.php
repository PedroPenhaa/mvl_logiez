<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestAsaas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:test 
                            {--operacao=todos : Operação a ser testada (todos, clientes, cobrancas, boleto, pix, qrcode, cartao)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a API do Asaas usando o ambiente Sandbox';

    /**
     * URL base do ambiente sandbox
     */
    protected $baseUrl = 'https://api-sandbox.asaas.com';

    /**
     * Token de acesso para o ambiente sandbox
     */
    protected $apiToken = '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6Ojk5YWQxY2M2LTg1ODUtNDA2YS04ZWRlLTAzNTY5NDRmYmM2Mjo6JGFhY2hfYTI0ZmIzYjUtMWRiOS00MmJiLWI1MjItYjk1ZWRjNTQxYjI5';

    /**
     * User-Agent padrão para todas as requisições
     */
    protected $userAgent = 'MVL Logiez/1.0 (API Test; logiez@example.com)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('💰 Testando API do Asaas (Sandbox)');
        $this->info('----------------------------------');

        $operacao = $this->option('operacao');
        $operacoesValidas = ['todos', 'clientes', 'cobrancas', 'boleto', 'pix', 'qrcode', 'cartao'];
        
        if (!in_array($operacao, $operacoesValidas)) {
            $this->error('Operação inválida: ' . $operacao);
            $this->line('Operações disponíveis: ' . implode(', ', $operacoesValidas));
            return Command::FAILURE;
        }
        
        $this->info('Operação selecionada: ' . strtoupper($operacao));
        $this->newLine();
        
        try {
            // ID do cliente criado para uso em outras operações
            $customerId = null;
            
            // Executar operações conforme a opção selecionada
            if (in_array($operacao, ['todos', 'clientes'])) {
                $customerId = $this->criarCliente();
            }
            
            if (in_array($operacao, ['todos', 'cobrancas', 'boleto'])) {
                if (!$customerId && $operacao !== 'todos') {
                    $customerId = $this->obterClienteExistente();
                }
                
                if ($customerId) {
                    $this->criarCobrancaBoleto($customerId);
                } else {
                    $this->warn('⚠️ Não foi possível criar cobrança: nenhum cliente disponível');
                }
            }
            
            if (in_array($operacao, ['todos', 'cobrancas', 'pix'])) {
                if (!$customerId && $operacao !== 'todos') {
                    $customerId = $this->obterClienteExistente();
                }
                
                if ($customerId) {
                    $this->criarCobrancaPix($customerId);
                } else {
                    $this->warn('⚠️ Não foi possível criar cobrança PIX: nenhum cliente disponível');
                }
            }
            
            if (in_array($operacao, ['todos', 'cobrancas', 'cartao'])) {
                if (!$customerId && $operacao !== 'todos') {
                    $customerId = $this->obterClienteExistente();
                }
                
                if ($customerId) {
                    $this->criarCobrancaCartao($customerId);
                } else {
                    $this->warn('⚠️ Não foi possível criar cobrança por cartão: nenhum cliente disponível');
                }
            }
            
            if (in_array($operacao, ['todos', 'qrcode'])) {
                $this->criarQrCodeEstatico();
            }
            
            if (in_array($operacao, ['todos', 'clientes'])) {
                $this->listarClientes();
            }

            if (in_array($operacao, ['todos', 'cobrancas'])) {
                $this->listarCobrancas();
            }
            
            // Resumo dos testes
            $this->newLine();
            $this->info('✅ Testes concluídos com sucesso!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erro ao testar API Asaas: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Realiza requisição para a API do Asaas
     *
     * @param string $endpoint Endpoint da API (sem a URL base)
     * @param string $method Método HTTP (GET, POST, PUT, DELETE)
     * @param array $payload Dados a serem enviados (para POST, PUT)
     * @return array Resposta da API decodificada
     * @throws \Exception
     */
    protected function fazerRequisicao($endpoint, $method = 'GET', $payload = null)
    {
        $this->line("Fazendo requisição {$method} para {$endpoint}...");
        
        $curl = curl_init();
        $url = $this->baseUrl . $endpoint;
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'access_token: ' . $this->apiToken,
                'User-Agent: ' . $this->userAgent
            ],
            CURLOPT_SSL_VERIFYPEER => false
        ];
        
        if ($payload && in_array($method, ['POST', 'PUT'])) {
            $options[CURLOPT_POSTFIELDS] = json_encode($payload);
        }
        
        curl_setopt_array($curl, $options);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            throw new \Exception('Erro na requisição para ' . $endpoint . ': ' . $err);
        }
        
        if ($httpCode < 200 || $httpCode >= 300) {
            $errorDetails = json_decode($response, true);
            $errorMessage = 'Resposta da API: ' . substr($response, 0, 500);
            throw new \Exception('Falha na requisição. Código HTTP: ' . $httpCode . "\n" . $errorMessage);
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Cria um novo cliente no Asaas
     *
     * @return string ID do cliente criado
     * @throws \Exception
     */
    protected function criarCliente()
    {
        $this->info('📋 Criando um cliente...');
        
        $clientePayload = [
            'name' => 'Cliente Teste',
            'email' => 'teste@exemplo.com',
            'phone' => '4832999999',
            'mobilePhone' => '48989999999',
            'cpfCnpj' => '24971563792',
            'postalCode' => '01310000',
            'address' => 'Avenida Paulista',
            'addressNumber' => '150',
            'complement' => 'Sala 10',
            'province' => 'Centro',
            'externalReference' => 'cliente-test-' . uniqid(),
            'notificationDisabled' => false,
            'additionalEmails' => 'email@exemplo.com',
            'municipalInscription' => '46683695908',
            'stateInscription' => '646681195275',
            'observations' => 'Cliente criado para teste da API'
        ];
        
        $clienteData = $this->fazerRequisicao('/v3/customers', 'POST', $clientePayload);
        $customerId = $clienteData['id'] ?? null;
        
        if (!$customerId) {
            throw new \Exception('Não foi possível obter o ID do cliente após criação.');
        }
        
        $this->info('✅ Cliente criado com sucesso!');
        $this->info("ID do Cliente: {$customerId}");
        $this->info("Nome: {$clienteData['name']}");
        $this->info("Email: {$clienteData['email']}");
        $this->info("CPF/CNPJ: {$clienteData['cpfCnpj']}");
        
        $this->newLine();
        
        return $customerId;
    }
    
    /**
     * Obtém um cliente existente para usar em testes
     *
     * @return string|null ID do cliente encontrado ou null se não houver clientes
     * @throws \Exception
     */
    protected function obterClienteExistente()
    {
        $this->info('🔍 Buscando cliente existente...');
        
        $clientesData = $this->fazerRequisicao('/v3/customers?limit=1');
        
        if (isset($clientesData['data']) && count($clientesData['data']) > 0) {
            $cliente = $clientesData['data'][0];
            $this->info("✅ Cliente encontrado: {$cliente['name']} (ID: {$cliente['id']})");
            return $cliente['id'];
        }
        
        $this->warn('⚠️ Nenhum cliente encontrado');
        return null;
    }
    
    /**
     * Cria uma cobrança via boleto
     *
     * @param string $customerId ID do cliente
     * @return string ID da cobrança criada
     * @throws \Exception
     */
    protected function criarCobrancaBoleto($customerId)
    {
        $this->info('📄 Criando uma cobrança via boleto...');
        
        $cobrancaPayload = [
            'customer' => $customerId,
            'billingType' => 'BOLETO',
            'dueDate' => date('Y-m-d', strtotime('+7 days')),
            'value' => 100.00,
            'description' => 'Cobrança de teste via API',
            'externalReference' => 'cobranca-test-' . uniqid(),
            'discount' => [
                'value' => 10.00,
                'dueDateLimitDays' => 0
            ],
            'fine' => [
                'value' => 1.00
            ],
            'interest' => [
                'value' => 2.00
            ],
            'postalService' => false
        ];
        
        $cobrancaData = $this->fazerRequisicao('/v3/payments', 'POST', $cobrancaPayload);
        $cobrancaId = $cobrancaData['id'] ?? null;
        
        if (!$cobrancaId) {
            throw new \Exception('Não foi possível obter o ID da cobrança após criação.');
        }
        
        $this->info('✅ Cobrança criada com sucesso!');
        $this->info("ID da Cobrança: {$cobrancaId}");
        $this->info("Valor: R$ {$cobrancaData['value']}");
        $this->info("Vencimento: {$cobrancaData['dueDate']}");
        $this->info("Status: {$cobrancaData['status']}");
        $this->info("Tipo: {$cobrancaData['billingType']}");
        
        // Obter a linha digitável do boleto
        $this->newLine();
        $this->info('🔍 Obtendo dados do boleto...');
        
        $boletoData = $this->fazerRequisicao('/v3/payments/' . $cobrancaId . '/identificationField');
        $linhaDigitavel = $boletoData['identificationField'] ?? 'Não disponível';
        
        $this->info("Linha Digitável: {$linhaDigitavel}");
        
        return $cobrancaId;
    }
    
    /**
     * Cria uma cobrança via PIX
     *
     * @param string $customerId ID do cliente
     * @return string ID da cobrança criada
     * @throws \Exception
     */
    protected function criarCobrancaPix($customerId)
    {
        $this->newLine();
        $this->info('📱 Criando uma cobrança via PIX...');
        
        $pixPayload = [
            'customer' => $customerId,
            'billingType' => 'PIX',
            'dueDate' => date('Y-m-d', strtotime('+3 days')),
            'value' => 75.50,
            'description' => 'Cobrança PIX de teste via API',
            'externalReference' => 'pix-test-' . uniqid(),
        ];
        
        $pixData = $this->fazerRequisicao('/v3/payments', 'POST', $pixPayload);
        $pixId = $pixData['id'] ?? null;
        
        if (!$pixId) {
            throw new \Exception('Não foi possível obter o ID da cobrança PIX após criação.');
        }
        
        $this->info('✅ Cobrança PIX criada com sucesso!');
        $this->info("ID da Cobrança PIX: {$pixId}");
        $this->info("Valor: R$ {$pixData['value']}");
        $this->info("Vencimento: {$pixData['dueDate']}");
        
        // Obter QR Code PIX
        $this->newLine();
        $this->info('🔄 Obtendo QR Code PIX...');
        
        $qrCodeData = $this->fazerRequisicao('/v3/payments/' . $pixId . '/pixQrCode');
        $qrCodePayload = $qrCodeData['payload'] ?? 'Não disponível';
        $qrCodeExpiration = $qrCodeData['expirationDate'] ?? 'Não disponível';
        
        $this->info("Payload PIX: {$qrCodePayload}");
        $this->info("Validade: {$qrCodeExpiration}");
        
        return $pixId;
    }
    
    /**
     * Cria um QR Code estático
     *
     * @return string|null ID do QR Code estático criado ou null se não foi possível criar
     * @throws \Exception
     */
    protected function criarQrCodeEstatico()
    {
        $this->newLine();
        $this->info('🔄 Criando QR Code estático PIX...');
        
        // Primeiro, obtém a chave pix da conta
        $keysData = $this->fazerRequisicao('/v3/pix/addressKeys');
        $addressKey = null;
        
        if (isset($keysData['data']) && count($keysData['data']) > 0) {
            foreach ($keysData['data'] as $key) {
                if (isset($key['status']) && $key['status'] === 'ACTIVE') {
                    $addressKey = $key['id'];
                    $this->info("✅ Chave PIX encontrada: " . ($key['key'] ?? 'N/A'));
                    break;
                }
            }
        }
        
        if (!$addressKey) {
            $this->info('⚠️ Nenhuma chave PIX ativa encontrada. Tentando criar uma chave aleatória...');
            
            // Criar uma chave PIX aleatória se não existir
            try {
                $createKeyData = $this->fazerRequisicao('/v3/pix/addressKeys', 'POST', ['type' => 'EVP']);
                $addressKey = $createKeyData['id'] ?? null;
                
                if (isset($createKeyData['key'])) {
                    $this->info('✅ Chave PIX criada: ' . $createKeyData['key']);
                    
                    // Esperar um pouco para a chave ser ativada
                    $this->info('Aguardando 3 segundos para a chave ser processada...');
                    sleep(3);
                    
                    // Verificar status da chave
                    $keyStatus = $this->fazerRequisicao('/v3/pix/addressKeys/' . $addressKey);
                    if (isset($keyStatus['status']) && $keyStatus['status'] !== 'ACTIVE') {
                        $this->warn('⚠️ A chave PIX ainda não está ativa (status: ' . $keyStatus['status'] . ')');
                        $this->warn('Ignorando teste de QR Code estático.');
                        return null;
                    }
                }
            } catch (\Exception $e) {
                $this->warn('Não foi possível criar uma chave PIX: ' . $e->getMessage());
                $this->warn('Ignorando teste de QR Code estático.');
                return null;
            }
        }
        
        if ($addressKey) {
            // Criar QR Code estático
            try {
                $staticQrPayload = [
                    'addressKey' => $addressKey,
                    'description' => 'QR Code estático de teste',
                    'value' => 50.00,
                    'format' => 'ALL',
                ];
                
                $this->info("Usando addressKey: " . $addressKey);
                $staticQrData = $this->fazerRequisicao('/v3/pix/qrCodes/static', 'POST', $staticQrPayload);
                $staticQrId = $staticQrData['id'] ?? null;
                
                $this->info('✅ QR Code estático PIX criado com sucesso!');
                $this->info("ID do QR Code: {$staticQrId}");
                $this->info("Valor: R$ {$staticQrData['value']}");
                $this->info("Descrição: {$staticQrData['description']}");
                $this->info("Payload: " . (isset($staticQrData['payload']) ? substr($staticQrData['payload'], 0, 50) . '...' : 'Não disponível'));
                
                return $staticQrId;
            } catch (\Exception $e) {
                $this->warn('Não foi possível criar QR Code estático: ' . $e->getMessage());
                return null;
            }
        }
        
        return null;
    }
    
    /**
     * Lista os clientes cadastrados
     *
     * @throws \Exception
     */
    protected function listarClientes()
    {
        $this->newLine();
        $this->info('📋 Listando clientes...');
        
        $listClientesData = $this->fazerRequisicao('/v3/customers?limit=3');
        $totalClientes = $listClientesData['totalCount'] ?? 0;
        
        $this->info("Total de clientes cadastrados: {$totalClientes}");
        if (isset($listClientesData['data']) && count($listClientesData['data']) > 0) {
            $this->info('Últimos 3 clientes:');
            foreach ($listClientesData['data'] as $cliente) {
                $this->info("- {$cliente['name']} (ID: {$cliente['id']})");
            }
        }
    }
    
    /**
     * Lista as cobranças cadastradas
     *
     * @throws \Exception
     */
    protected function listarCobrancas()
    {
        $this->newLine();
        $this->info('📋 Listando cobranças...');
        
        $listCobrancasData = $this->fazerRequisicao('/v3/payments?limit=3');
        $totalCobrancas = $listCobrancasData['totalCount'] ?? 0;
        
        $this->info("Total de cobranças cadastradas: {$totalCobrancas}");
        if (isset($listCobrancasData['data']) && count($listCobrancasData['data']) > 0) {
            $this->info('Últimas 3 cobranças:');
            foreach ($listCobrancasData['data'] as $cobranca) {
                $this->info("- R$ {$cobranca['value']} - {$cobranca['billingType']} - {$cobranca['status']} (ID: {$cobranca['id']})");
            }
        }
    }

    /**
     * Cria uma cobrança via cartão de crédito
     *
     * @param string $customerId ID do cliente
     * @return string ID da cobrança criada
     * @throws \Exception
     */
    protected function criarCobrancaCartao($customerId)
    {
        $this->newLine();
        $this->info('💳 Criando uma cobrança via cartão de crédito...');
        
        // 1. Primeiro, criar uma cobrança
        $cobrancaPayload = [
            'customer' => $customerId,
            'billingType' => 'CREDIT_CARD',
            'dueDate' => date('Y-m-d'),
            'value' => 89.90,
            'description' => 'Cobrança por cartão de crédito via API',
            'externalReference' => 'cartao-test-' . uniqid(),
            'remoteIp' => '116.213.42.150', // IP fictício para teste
        ];
        
        $cobrancaData = $this->fazerRequisicao('/v3/payments', 'POST', $cobrancaPayload);
        $cobrancaId = $cobrancaData['id'] ?? null;
        
        if (!$cobrancaId) {
            throw new \Exception('Não foi possível obter o ID da cobrança após criação.');
        }
        
        $this->info('✅ Cobrança criada com sucesso!');
        $this->info("ID da Cobrança: {$cobrancaId}");
        $this->info("Valor: R$ {$cobrancaData['value']}");
        $this->info("Tipo: {$cobrancaData['billingType']}");
        
        // 2. Agora realizar o pagamento com cartão de crédito
        $this->info('💳 Processando pagamento com cartão de crédito...');
        
        // Cartão de teste conforme documentação
        $cartaoPayload = [
            'customer' => $customerId,
            'billingType' => 'CREDIT_CARD',
            'creditCard' => [
                'holderName' => 'Tester Testson',
                'number' => '4444444444444444',
                'expiryMonth' => '12',
                'expiryYear' => date('Y', strtotime('+2 years')),
                'ccv' => '123'
            ],
            'creditCardHolderInfo' => [
                'name' => 'Tester Testson',
                'email' => 'test@example.com',
                'cpfCnpj' => '24971563792',
                'postalCode' => '01310000',
                'addressNumber' => '150',
                'addressComplement' => 'Sala 10',
                'phone' => '4832999999',
                'mobilePhone' => '48989999999'
            ],
            'remoteIp' => '116.213.42.150'
        ];
        
        try {
            $pagamentoEndpoint = '/v3/payments/' . $cobrancaId . '/payWithCreditCard';
            $pagamentoData = $this->fazerRequisicao($pagamentoEndpoint, 'POST', $cartaoPayload);
            
            $this->info('✅ Pagamento com cartão processado com sucesso!');
            $this->info("Status: {$pagamentoData['status']}");
            
            if (isset($pagamentoData['creditCard'])) {
                $this->info("Cartão: {$pagamentoData['creditCard']['creditCardBrand']} - Final {$pagamentoData['creditCard']['creditCardNumber']}");
            }
            
            // Criar uma nova cobrança para testar o cenário de falha
            $this->info('🧪 Testando pagamento com cartão que deve falhar...');
            $this->info('Criando nova cobrança para o teste de falha...');
            
            $cobrancaFalhaPayload = $cobrancaPayload;
            $cobrancaFalhaPayload['externalReference'] = 'cartao-falha-test-' . uniqid();
            
            $cobrancaFalhaData = $this->fazerRequisicao('/v3/payments', 'POST', $cobrancaFalhaPayload);
            $cobrancaFalhaId = $cobrancaFalhaData['id'] ?? null;
            
            if ($cobrancaFalhaId) {
                $this->info("Nova cobrança criada para teste de falha: {$cobrancaFalhaId}");
                
                $cartaoFalhaPayload = $cartaoPayload;
                $cartaoFalhaPayload['creditCard']['number'] = '5184019740373151'; // Cartão de teste para falha
                
                try {
                    $pagamentoFalhaEndpoint = '/v3/payments/' . $cobrancaFalhaId . '/payWithCreditCard';
                    $pagamentoFalhaData = $this->fazerRequisicao($pagamentoFalhaEndpoint, 'POST', $cartaoFalhaPayload);
                    $this->warn('⚠️ O pagamento deveria ter falhado, mas foi processado.');
                } catch (\Exception $e) {
                    $this->info('✅ Testado cenário de falha no pagamento com sucesso: ' . $e->getMessage());
                }
            }
            
            return $cobrancaId;
        } catch (\Exception $e) {
            $this->warn('⚠️ Falha ao processar pagamento com cartão: ' . $e->getMessage());
            
            // Testar com um cartão tokenizado como alternativa
            $this->info('🔄 Tentando com tokenização de cartão...');
            
            try {
                // 1. Tokenizar o cartão
                $tokenizarEndpoint = '/v3/creditCard/tokenize';
                $tokenizarPayload = [
                    'customer' => $customerId,
                    'creditCard' => [
                        'holderName' => 'Tester Testson',
                        'number' => '4444444444444444',
                        'expiryMonth' => '12',
                        'expiryYear' => date('Y', strtotime('+2 years')),
                        'ccv' => '123'
                    ],
                    'creditCardHolderInfo' => [
                        'name' => 'Tester Testson',
                        'email' => 'test@example.com',
                        'cpfCnpj' => '24971563792',
                        'postalCode' => '01310000',
                        'addressNumber' => '150',
                        'addressComplement' => 'Sala 10',
                        'phone' => '4832999999',
                        'mobilePhone' => '48989999999'
                    ],
                    'remoteIp' => '116.213.42.150'
                ];
                
                $tokenizarData = $this->fazerRequisicao($tokenizarEndpoint, 'POST', $tokenizarPayload);
                $tokenCartao = $tokenizarData['creditCardToken'] ?? null;
                
                if (!$tokenCartao) {
                    throw new \Exception('Não foi possível obter o token do cartão.');
                }
                
                $this->info("✅ Cartão tokenizado com sucesso: {$tokenCartao}");
                
                // 2. Realizar o pagamento com o token
                $pagamentoTokenEndpoint = '/v3/payments/' . $cobrancaId . '/payWithCreditCard';
                $pagamentoTokenPayload = [
                    'customer' => $customerId,
                    'billingType' => 'CREDIT_CARD',
                    'creditCardToken' => $tokenCartao,
                    'remoteIp' => '116.213.42.150'
                ];
                
                $pagamentoTokenData = $this->fazerRequisicao($pagamentoTokenEndpoint, 'POST', $pagamentoTokenPayload);
                
                $this->info('✅ Pagamento com cartão tokenizado processado com sucesso!');
                $this->info("Status: {$pagamentoTokenData['status']}");
                
                if (isset($pagamentoTokenData['creditCard'])) {
                    $this->info("Cartão: {$pagamentoTokenData['creditCard']['creditCardBrand']} - Final {$pagamentoTokenData['creditCard']['creditCardNumber']}");
                }
                
                return $cobrancaId;
            } catch (\Exception $tokenError) {
                $this->warn('⚠️ Falha também ao usar cartão tokenizado: ' . $tokenError->getMessage());
                throw $e; // Relanço a exceção original
            }
        }
    }
} 