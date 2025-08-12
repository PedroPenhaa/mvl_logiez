<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Shipment;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\UserProfile;
use App\Models\SenderAddress;
use App\Models\RecipientAddress;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\ProofOfDelivery;
use App\Models\SavedAddress;
use App\Models\UserSetting;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\ApiLog;
use App\Models\ShippingRate;
use App\Models\FedexLabel;
use App\Models\Cache;

class AdminController extends Controller
{
    /**
     * Exibe o painel administrativo principal
     */
    public function index()
    {
        // Estatísticas gerais
        $stats = [
            'total_users' => User::count(),
            'total_shipments' => Shipment::count(),
            'total_payments' => Payment::count(),
            'total_quotes' => Quote::count(),
            'active_users' => User::where('is_active', true)->count(),
            'pending_shipments' => Shipment::where('status', 'pending')->count(),
            'completed_shipments' => Shipment::where('status', 'delivered')->count(),
            'total_revenue' => Payment::where('status', 'confirmed')->sum('amount'),
        ];

        // Dados recentes
        $recent_users = User::latest()->take(5)->get();
        $recent_shipments = Shipment::with('user')->latest()->take(5)->get();
        $recent_payments = Payment::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_shipments', 'recent_payments'));
    }

    /**
     * Lista todos os usuários
     */
    public function users()
    {
        $users = User::with('profile')->latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Lista todos os envios
     */
    public function shipments()
    {
        $shipments = Shipment::with(['user', 'quote'])->latest()->paginate(20);
        return view('admin.shipments', compact('shipments'));
    }

    /**
     * Lista todos os pagamentos
     */
    public function payments()
    {
        $payments = Payment::with(['user', 'shipment'])->latest()->paginate(20);
        return view('admin.payments', compact('payments'));
    }

    /**
     * Lista todas as cotações
     */
    public function quotes()
    {
        $quotes = Quote::with('user')->latest()->paginate(20);
        return view('admin.quotes', compact('quotes'));
    }

    /**
     * Lista todos os endereços salvos
     */
    public function addresses()
    {
        $senderAddresses = SenderAddress::with('user')->latest()->paginate(15);
        $recipientAddresses = RecipientAddress::with('user')->latest()->paginate(15);
        return view('admin.addresses', compact('senderAddresses', 'recipientAddresses'));
    }

    /**
     * Lista todos os itens de envio
     */
    public function items()
    {
        $items = ShipmentItem::with(['shipment'])->latest()->paginate(20);
        return view('admin.items', compact('items'));
    }

    /**
     * Lista todos os eventos de rastreamento
     */
    public function tracking()
    {
        $events = TrackingEvent::with('shipment')->latest()->paginate(20);
        return view('admin.tracking', compact('events'));
    }

    /**
     * Lista todas as provas de entrega
     */
    public function proofOfDelivery()
    {
        $proofs = ProofOfDelivery::with('shipment')->latest()->paginate(20);
        return view('admin.proof_of_delivery', compact('proofs'));
    }

    /**
     * Lista todas as notificações
     */
    public function notifications()
    {
        $notifications = Notification::with('user')->latest()->paginate(20);
        return view('admin.notifications', compact('notifications'));
    }

    /**
     * Lista todos os logs de atividade
     */
    public function activityLogs()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('admin.activity_logs', compact('logs'));
    }

    /**
     * Lista todos os logs da API
     */
    public function apiLogs()
    {
        $logs = ApiLog::latest()->paginate(20);
        return view('admin.api_logs', compact('logs'));
    }

    /**
     * Lista todas as taxas de envio
     */
    public function shippingRates()
    {
        $rates = ShippingRate::latest()->paginate(20);
        return view('admin.shipping_rates', compact('rates'));
    }

    /**
     * Lista todas as etiquetas FedEx
     */
    public function fedexLabels()
    {
        $labels = FedexLabel::with('shipment')->latest()->paginate(20);
        return view('admin.fedex_labels', compact('labels'));
    }

    /**
     * Lista todos os dados em cache
     */
    public function cache()
    {
        $cache = Cache::latest()->paginate(20);
        return view('admin.cache', compact('cache'));
    }

    /**
     * Exibe detalhes de um usuário específico
     */
    public function userDetails($id)
    {
        $user = User::with(['profile', 'shipments', 'payments', 'quotes'])->findOrFail($id);
        return view('admin.user_details', compact('user'));
    }

    /**
     * Exibe detalhes de um envio específico
     */
    public function shipmentDetails($id)
    {
        $shipment = Shipment::with(['user', 'quote', 'items', 'trackingEvents', 'proofOfDelivery'])->findOrFail($id);
        return view('admin.shipment_details', compact('shipment'));
    }

    /**
     * Exibe detalhes de um pagamento específico
     */
    public function paymentDetails($id)
    {
        $payment = Payment::with(['user', 'shipment'])->findOrFail($id);
        return view('admin.payment_details', compact('payment'));
    }
}
