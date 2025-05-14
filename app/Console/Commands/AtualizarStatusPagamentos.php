<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class AtualizarStatusPagamentos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:atualizar-status-pagamentos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica o status dos pagamentos na API do Asaas e atualiza os registros no sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificação de status de pagamentos...');
        
        // Buscar pagamentos pendentes ou em processamento
        $pagamentos = Payment::whereIn('status', ['pending', 'processing'])
            ->whereNotNull('transaction_id')
            ->where('payment_gateway', 'asaas')
            ->limit(20) // Processa em lotes
            ->get();
        
        $this->info("Encontrados {$pagamentos->count()} pagamentos para verificar.");
        
        if ($pagamentos->isEmpty()) {
            $this->info('Nenhum pagamento pendente para verificar.');
            return 0;
        }
        
        // Configuração para API do Asaas
        $baseUrl = config('services.asaas.sandbox') 
            ? 'https://api-sandbox.asaas.com' 
            : 'https://api.asaas.com';
        
        $apiToken = config('services.asaas.token');
        
        foreach ($pagamentos as $pagamento) {
            $this->info("Verificando pagamento #{$pagamento->id} - Transação: {$pagamento->transaction_id}");
            
            try {
                // Fazer requisição à API do Asaas para obter o status atual
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $baseUrl . '/v3/payments/' . $pagamento->transaction_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'access_token: ' . $apiToken,
                        'User-Agent: LogiEZ/1.0'
                    ],
                    CURLOPT_SSL_VERIFYPEER => false
                ]);
                
                // Realizar a requisição e obter a resposta
                $response = curl_exec($curl);
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $err = curl_error($curl);
                
                curl_close($curl);
                
                // Log para debug
                Log::info('AtualizarStatusPagamentos: Resposta do Asaas', [
                    'payment_id' => $pagamento->id,
                    'transaction_id' => $pagamento->transaction_id,
                    'http_code' => $httpCode,
                    'response' => $response,
                    'error' => $err
                ]);
                
                // Se ocorrer erro na requisição
                if ($err) {
                    $this->error("Erro na requisição para Asaas: {$err}");
                    continue;
                }
                
                // Se a resposta for bem-sucedida
                if ($httpCode >= 200 && $httpCode < 300) {
                    $responseData = json_decode($response, true);
                    
                    // Status atual do pagamento no Asaas
                    $statusAsaas = $responseData['status'] ?? null;
                    
                    // Log de console para debug
                    $this->info("Status atual no Asaas: {$statusAsaas}");
                    
                    // Se o status é válido, atualizar no sistema
                    if ($statusAsaas) {
                        $statusAnterior = $pagamento->status;
                        
                        // Mapear status do Asaas para o sistema
                        $novoStatus = $this->mapearStatus($statusAsaas);
                        
                        // Atualizar apenas se o status mudou
                        if ($statusAnterior != $novoStatus) {
                            $pagamento->status = $novoStatus;
                            
                            // Se o pagamento foi confirmado, atualizar a data do pagamento
                            if (in_array($novoStatus, ['confirmed', 'received'])) {
                                $pagamento->payment_date = now();
                            }
                            
                            // Salvar os dados do pagamento
                            $pagamento->save();
                            
                            $this->info("Status atualizado: {$statusAnterior} -> {$novoStatus}");
                            
                            // Log para registro da alteração
                            Log::info('AtualizarStatusPagamentos: Status atualizado', [
                                'payment_id' => $pagamento->id,
                                'transaction_id' => $pagamento->transaction_id,
                                'status_anterior' => $statusAnterior,
                                'novo_status' => $novoStatus
                            ]);
                        } else {
                            $this->info("Status não mudou: {$statusAnterior}");
                        }
                    } else {
                        $this->warn("Status não encontrado na resposta do Asaas.");
                    }
                } else {
                    $this->error("Erro ao verificar pagamento: HTTP {$httpCode}");
                    Log::error('AtualizarStatusPagamentos: Erro ao verificar pagamento', [
                        'payment_id' => $pagamento->id,
                        'transaction_id' => $pagamento->transaction_id,
                        'http_code' => $httpCode,
                        'response' => $response
                    ]);
                }
                
            } catch (\Exception $e) {
                $this->error("Erro ao processar pagamento #{$pagamento->id}: " . $e->getMessage());
                Log::error('AtualizarStatusPagamentos: Erro ao processar pagamento', [
                    'payment_id' => $pagamento->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info('Verificação de status de pagamentos concluída.');
        return 0;
    }
    
    /**
     * Mapeia os status da API do Asaas para os status do sistema
     *
     * @param string $statusAsaas
     * @return string
     */
    private function mapearStatus($statusAsaas)
    {
        $mapeamento = [
            'PENDING' => 'pending',
            'RECEIVED' => 'received',
            'CONFIRMED' => 'confirmed',
            'OVERDUE' => 'overdue',
            'REFUNDED' => 'refunded',
            'RECEIVED_IN_CASH' => 'received',
            'REFUND_REQUESTED' => 'refund_requested',
            'CHARGEBACK_REQUESTED' => 'chargeback',
            'CHARGEBACK_DISPUTE' => 'chargeback',
            'AWAITING_CHARGEBACK_REVERSAL' => 'chargeback',
            'DUNNING_REQUESTED' => 'dunning',
            'DUNNING_RECEIVED' => 'dunning',
            'AWAITING_RISK_ANALYSIS' => 'processing'
        ];
        
        return $mapeamento[$statusAsaas] ?? 'pending';
    }
} 