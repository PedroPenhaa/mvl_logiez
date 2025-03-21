<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'welcome']);
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