<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Exibe a lista de pagamentos do usuário logado.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar seus pagamentos.');
        }

        // Buscar os pagamentos do usuário logado
        $pendingPayments = Payment::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $completedPayments = Payment::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $cancelledPayments = Payment::where('user_id', Auth::id())
            ->where('status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sections.pagamento', compact('pendingPayments', 'completedPayments', 'cancelledPayments'));
    }

    /**
     * Exibe os detalhes de um pagamento específico.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Buscar o pagamento e verificar se pertence ao usuário logado
        $payment = Payment::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('sections.pagamento_detalhes', compact('payment'));
    }

    /**
     * Processa um novo pagamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'metodo' => 'required|in:cartao,boleto,pix',
            'valorTotal' => 'required|numeric',
            'codigoEnvio' => 'required|string',
            'shipment_id' => 'required|exists:shipments,id',
        ]);
        
        // Validação específica para cartão de crédito
        if ($request->metodo === 'cartao') {
            $request->validate([
                'cartao_numero' => 'required|string',
                'cartao_nome' => 'required|string',
                'cartao_validade' => 'required|string',
                'cartao_cvv' => 'required|string',
                'cartao_parcelas' => 'required|integer|min:1|max:12',
            ]);
        }
        
        // Gerar código de transação
        $codigoTransacao = 'TRX' . rand(10000000, 99999999);
        
        try {
            // Criar o registro de pagamento vinculado ao usuário logado
            $payment = new Payment();
            $payment->user_id = Auth::id();
            $payment->shipment_id = $request->shipment_id;
            $payment->transaction_id = $codigoTransacao;
            $payment->payment_method = $request->metodo;
            $payment->payment_gateway = 'sistema'; // Definir o gateway utilizado
            $payment->amount = $request->valorTotal;
            $payment->currency = 'BRL';
            $payment->status = 'pending';
            $payment->due_date = now()->addDays(3); // 3 dias para vencimento
            $payment->payer_name = Auth::user()->name;
            $payment->payer_email = Auth::user()->email;
            
            // Gerar os links de pagamento de acordo com o método
            if ($request->metodo === 'boleto') {
                $payment->payment_link = route('payments.boleto', ['transactionId' => $codigoTransacao]);
                $payment->barcode = '34191.79001 01043.510047 91020.150008 6 ' . rand(10000000000, 99999999999);
            } else if ($request->metodo === 'pix') {
                $payment->payment_link = route('payments.pix', ['transactionId' => $codigoTransacao]);
                $payment->qrcode = 'data:image/png;base64,' . base64_encode('QRCODE-PIX-SIMULADO-' . $codigoTransacao);
            }
            
            $payment->save();
            
            return response()->json([
                'success' => true,
                'codigoTransacao' => $codigoTransacao,
                'codigoEnvio' => $request->codigoEnvio,
                'valorPago' => $request->valorTotal,
                'metodoPagamento' => $request->metodo,
                'payment_id' => $payment->id,
                'message' => 'Pagamento registrado com sucesso.',
                'nextStep' => 'pagamento'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao processar pagamento', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'shipment_id' => $request->shipment_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe o QR Code para pagamento PIX.
     *
     * @param  string  $transactionId
     * @return \Illuminate\Http\Response
     */
    public function showPix($transactionId)
    {
        // Buscar o pagamento pelo código de transação e verificar se pertence ao usuário logado
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->where('payment_method', 'pix')
            ->firstOrFail();

        return view('payments.pix', compact('payment'));
    }

    /**
     * Exibe o boleto para pagamento.
     *
     * @param  string  $transactionId
     * @return \Illuminate\Http\Response
     */
    public function showBoleto($transactionId)
    {
        // Buscar o pagamento pelo código de transação e verificar se pertence ao usuário logado
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->where('payment_method', 'boleto')
            ->firstOrFail();

        return view('payments.boleto', compact('payment'));
    }

    /**
     * Simula o callback de confirmação de pagamento (para testes).
     *
     * @param  string  $transactionId
     * @return \Illuminate\Http\Response
     */
    public function simulateCallback($transactionId)
    {
        // Buscar o pagamento pelo código de transação
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();
        
        // Atualizar o status para concluído
        $payment->status = 'completed';
        $payment->payment_date = now();
        $payment->save();
        
        return redirect()->route('payments.index')->with('success', 'Pagamento confirmado com sucesso!');
    }
} 