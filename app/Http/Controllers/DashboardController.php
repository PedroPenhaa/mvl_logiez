<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Buscar envios em andamento do usuário logado
        $enviosEmAndamento = Shipment::where('user_id', Auth::id())
            ->whereIn('status', ['PENDING', 'IN_TRANSIT', 'PROCESSING', 'PICKED_UP', 'IN_DELIVERY'])
            ->with(['senderAddress', 'recipientAddress', 'trackingEvents'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('enviosEmAndamento'));
    }
} 