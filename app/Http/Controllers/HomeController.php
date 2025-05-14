<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shipment;
use App\Models\Payment;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware pode ser configurado diretamente nas rotas
    }

    /**
     * Exibe a página inicial do sistema (página de boas-vindas).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        return view('welcome');
    }

    /**
     * Exibe o formulário de login.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Processa a autenticação do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        // Autenticação temporária: qualquer email/senha é aceito
        // Quando o banco de dados estiver pronto, substituir pelo código comentado abaixo
        session(['user_email' => $request->email]);
        session(['authenticated' => true]);
 
        return redirect()->intended('dashboard');
 
        /* Código para autenticação real com banco de dados
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect()->intended('dashboard');
        }
 
        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
        */
    }

    /**
     * Encerra a sessão do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect('/');
    }

    /**
     * Exibe o dashboard principal após o login.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Em vez de apenas retornar a view, vamos buscar a seção dashboard
        // e retornar essa view com o conteúdo
        return view('sections.dashboard');
    }

    /**
     * Mostra a página de sobre com informações do sistema.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about()
    {
        $info = [
            'name' => 'Logiez - Sistema de Envios Internacionais',
            'version' => '1.0.0',
            'description' => 'Plataforma de cotação e gestão de envios internacionais',
            'company' => 'Logiez International Shipping',
            'partners' => ['DHL', 'FedEx', 'UPS'],
            'contact' => 'contato@logiez.com.br'
        ];
        
        return view('about', compact('info'));
    }
    
    /**
     * Exibe a página de ajuda e FAQs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function help()
    {
        $faqs = [
            [
                'question' => 'Como faço uma cotação?',
                'answer' => 'Acesse a seção "Cotação" e preencha os dados de origem, destino e detalhes do pacote.'
            ],
            [
                'question' => 'Quais países são atendidos?',
                'answer' => 'Atendemos envios para mais de 150 países através das nossas parceiras internacionais.'
            ],
            [
                'question' => 'Como rastreio meu pacote?',
                'answer' => 'Acesse a seção "Rastreamento" e digite o código de rastreio que foi enviado para seu email.'
            ],
            [
                'question' => 'Quais formas de pagamento são aceitas?',
                'answer' => 'Aceitamos cartão de crédito, boleto bancário e PIX.'
            ],
            [
                'question' => 'O que fazer se meu pacote atrasar?',
                'answer' => 'Entre em contato com nosso suporte pelo email suporte@logiez.com.br informando o código de rastreio.'
            ]
        ];
        
        return view('help', compact('faqs'));
    }

    /**
     * Exibe o formulário de registro.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function register()
    {
        return view('register');
    }

    /**
     * Processa o registro de um novo usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Autenticação temporária: apenas simula o cadastro
        // Quando o banco de dados estiver pronto, substituir pelo código adequado
        session(['user_email' => $request->email]);
        session(['user_name' => $request->name]);
        session(['authenticated' => true]);

        return redirect()->route('dashboard')->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Exibe o dashboard do usuário com resumo de serviços.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Obter últimos 5 envios do usuário
        $shipments = \App\Models\Shipment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Obter últimos 3 pagamentos pendentes
        $pendingPayments = \App\Models\Payment::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Obter últimos 3 pagamentos concluídos
        $completedPayments = \App\Models\Payment::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Retornar a view com os dados
        return view('dashboard', [
            'dashboardContent' => view('sections.dashboard_resumo', compact('shipments', 'pendingPayments', 'completedPayments'))->render()
        ]);
    }
} 