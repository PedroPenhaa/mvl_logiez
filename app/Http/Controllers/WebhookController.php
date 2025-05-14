<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Recebe webhooks do Asaas para atualização de status de pagamento
     */
    public function asaasWebhook(Request $request)
    {
        // Log para depuração
        Log::info('Webhook Asaas recebido', [
            'headers' => $request->header(),
            'body' => $request->all()
        ]);
        
        // Registrar no histórico de API
        $apiLog = new ApiLog();
        $apiLog->api_service = 'asaas_webhook';
        $apiLog->endpoint = 'webhook/asaas';
        $apiLog->http_method = 'POST';
        $apiLog->request_data = json_encode($request->all());
        $apiLog->ip_address = $request->ip();
        $apiLog->save();
        
        try {
            // Verificar se é um evento de pagamento
            $event = $request->input('event');
            if (!$event || !str_contains($event, 'PAYMENT_')) {
                return response()->json(['message' => 'Evento não relacionado a pagamento'], 200);
            }
            
            // Obter o ID da transação (payment)
            $paymentId = $request->input('payment.id');
            if (!$paymentId) {
                return response()->json(['message' => 'ID de pagamento não encontrado'], 400);
            }
            
            // Buscar o pagamento correspondente no sistema
            $payment = Payment::where('transaction_id', $paymentId)->first();
            if (!$payment) {
                return response()->json(['message' => 'Pagamento não encontrado no sistema'], 404);
            }
            
            // Obter o status atual no Asaas
            $asaasStatus = $request->input('payment.status');
            
            // Mapear status do Asaas para o sistema
            $novoStatus = $this->mapearStatus($asaasStatus);
            $statusAnterior = $payment->status;
            
            // Registrar no log
            Log::info('Atualizando status de pagamento via webhook', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'status_anterior' => $statusAnterior,
                'status_asaas' => $asaasStatus,
                'novo_status' => $novoStatus
            ]);
            
            // Atualizar o status do pagamento
            $payment->status = $novoStatus;
            
            // Se confirmado, atualizar a data do pagamento
            if (in_array($novoStatus, ['confirmed', 'received']) && !$payment->payment_date) {
                $payment->payment_date = now();
            }
            
            // Atualizar payload completo da resposta
            $payment->gateway_response = json_encode($request->input('payment'));
            $payment->save();
            
            // Atualizar o registro em api_logs
            $apiLog->status = 'success';
            $apiLog->response_code = 200;
            $apiLog->save();
            
            // Se o pagamento foi confirmado e o envio está pendente, processar o envio
            if (in_array($novoStatus, ['confirmed', 'received']) && $payment->shipment_id) {
                $shipment = Shipment::find($payment->shipment_id);
                
                if ($shipment && $shipment->status === 'pending_payment') {
                    // Marcar para processamento posterior (via comando agendado)
                    Log::info('Envio marcado para processamento', [
                        'shipment_id' => $shipment->id,
                        'payment_id' => $payment->id,
                        'novo_status' => $novoStatus
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status de pagamento atualizado com sucesso',
                'payment_id' => $payment->id,
                'novo_status' => $novoStatus
            ]);
            
        } catch (\Exception $e) {
            // Registrar erro no log
            Log::error('Erro ao processar webhook do Asaas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Atualizar o registro em api_logs
            $apiLog->status = 'error';
            $apiLog->error_message = $e->getMessage();
            $apiLog->save();
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar webhook: ' . $e->getMessage()
            ], 500);
        }
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
