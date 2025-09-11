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
        // Middleware já está definido nas rotas
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
        return view('dashboard');
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
        // Log para debug
        \Log::info('Dashboard - Auth check: ' . (Auth::check() ? 'true' : 'false'));
        \Log::info('Dashboard - User ID: ' . (Auth::check() ? Auth::id() : 'null'));
        \Log::info('Dashboard - User email: ' . (Auth::check() ? Auth::user()->email : 'null'));
        \Log::info('Dashboard - Session ID: ' . session()->getId());
        \Log::info('Dashboard - Session data: ' . json_encode(session()->all()));
        
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            // Tentar autenticar via dados da sessão como fallback
            if (session('user_id') && session('user_email')) {
                $user = User::find(session('user_id'));
                if ($user && $user->email === session('user_email')) {
                    Auth::login($user, true);
                    \Log::info('Dashboard - Usuário autenticado via sessão fallback: ' . $user->email);
                }
            }
            
            // Se ainda não estiver autenticado, redirecionar para login
            if (!Auth::check()) {
                \Log::info('Dashboard - Usuário não autenticado, redirecionando para login');
                \Log::info('Dashboard - Session data quando não autenticado: ' . json_encode(session()->all()));
                return redirect()->route('login')->with('error', 'Você precisa fazer login para acessar esta página.');
            }
        }
        
        \Log::info('Dashboard - Usuário autenticado com sucesso, exibindo dashboard');
        \Log::info('Dashboard - User ID final: ' . Auth::id());
        \Log::info('Dashboard - User email final: ' . Auth::user()->email);
        
        // Buscar envios do usuário que têm tracking_number (mesma lógica da tela de etiquetas)
        $enviosEmAndamento = Shipment::where('user_id', Auth::id())
            ->whereNotNull('tracking_number')
            ->with(['senderAddress', 'recipientAddress', 'trackingEvents'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact('enviosEmAndamento'));
    }
} 