<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Mostra o formulário de login
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Processa a tentativa de login
     */
    public function login(Request $request)
    {
        // Temporariamente sem validação de login
        // Simplesmente redireciona para o dashboard
        return redirect()->route('dashboard');
    }

    /**
     * Faz logout do usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 