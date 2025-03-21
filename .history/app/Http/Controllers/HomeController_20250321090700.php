<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
} 