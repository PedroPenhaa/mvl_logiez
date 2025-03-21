<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function dashboard()
    {
        return view('sections.dashboard');
    }
    
    public function cotacao()
    {
        return view('sections.cotacao');
    }
    
    public function envio()
    {
        return view('sections.envio');
    }
    
    public function pagamento()
    {
        return view('sections.pagamento');
    }
    
    public function etiqueta()
    {
        return view('sections.etiqueta');
    }
    
    public function rastreamento()
    {
        return view('sections.rastreamento');
    }
    
    public function perfil()
    {
        // Simulação de dados do usuário para o exemplo
        $user = [
            'nome' => 'João Silva',
            'email' => 'joao.silva@exemplo.com',
            'cpf' => '123.456.789-00',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01310-100',
            'rua' => 'Avenida Paulista',
            'numero' => '1000',
            'complemento' => 'Apto 123',
            'telefone' => '(11) 98765-4321'
        ];
        
        return view('sections.perfil', ['usuario' => $user]);
    }

    /**
     * Processa o cálculo de cotação de envio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calcularCotacao(Request $request)
    {
        // Validar os dados de entrada
        $request->validate([
            'origem' => 'required|string',
            'destino' => 'required|string',
            'altura' => 'required|numeric|min:0',
            'largura' => 'required|numeric|min:0',
            'comprimento' => 'required|numeric|min:0',
            'peso' => 'required|numeric|min:0',
        ]);
        
        // Simulação de cálculo de cotação
        // Em produção, aqui seria feita integração com a API da DHL
        
        // Cálculo do peso cúbico (dimensional)
        $pesoCubico = ($request->altura * $request->largura * $request->comprimento) / 5000;
        
        // Usar o maior entre peso real e peso cúbico
        $pesoUtilizado = max($pesoCubico, $request->peso);
        
        // Simular cálculo de frete
        $valorFrete = $pesoUtilizado * 50; // Exemplo: R$ 50 por kg
        
        // Adicionar taxa de serviço
        $taxaServico = $valorFrete * 0.15; // 15% de taxa
        
        // Valor total
        $valorTotal = $valorFrete + $taxaServico;
        
        // Tempo estimado (simulação)
        $tempoEntrega = rand(3, 10); // Entre 3 e 10 dias
        
        return response()->json([
            'success' => true,
            'pesoCubico' => round($pesoCubico, 2),
            'pesoReal' => $request->peso,
            'pesoUtilizado' => round($pesoUtilizado, 2),
            'valorFrete' => round($valorFrete, 2),
            'taxaServico' => round($taxaServico, 2),
            'valorTotal' => round($valorTotal, 2),
            'tempoEntrega' => $tempoEntrega,
            'moeda' => 'BRL'
        ]);
    }
    
    /**
     * Processa os dados de envio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarEnvio(Request $request)
    {
        // Validar os dados de entrada
        $request->validate([
            'remetente_nome' => 'required|string',
            'remetente_email' => 'required|email',
            'remetente_telefone' => 'required|string',
            'remetente_tipo' => 'required|in:fisica,juridica',
            'remetente_documento' => 'required|string',
            
            'destinatario_nome' => 'required|string',
            'destinatario_email' => 'required|email',
            'destinatario_telefone' => 'required|string',
            'destinatario_endereco' => 'required|string',
            'destinatario_cidade' => 'required|string',
            'destinatario_pais' => 'required|string',
            'destinatario_cep' => 'required|string',
            
            'mercadoria_tipo' => 'required|string',
            'mercadoria_valor' => 'required|numeric',
            'mercadoria_descricao' => 'required|string',
            'mercadoria_altura' => 'required|numeric',
            'mercadoria_largura' => 'required|numeric',
            'mercadoria_comprimento' => 'required|numeric',
            'mercadoria_peso' => 'required|numeric',
            'mercadoria_liquido' => 'required|boolean',
        ]);
        
        // Gerar código de envio (simulação)
        $codigoEnvio = 'DHL' . rand(100000000, 999999999);
        
        // Em produção, aqui seria feita a gravação no banco de dados
        // e integração com a API da DHL para iniciar o processo de envio
        
        return response()->json([
            'success' => true,
            'codigoEnvio' => $codigoEnvio,
            'message' => 'Dados de envio processados com sucesso.',
            'nextStep' => 'pagamento'
        ]);
    }
    
    /**
     * Processa o pagamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processarPagamento(Request $request)
    {
        // Validação depende do método de pagamento
        $request->validate([
            'metodo' => 'required|in:cartao,boleto,pix',
            'valorTotal' => 'required|numeric',
            'codigoEnvio' => 'required|string',
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
        
        // Simulação de processamento de pagamento
        // Em produção, aqui seria feita a integração com gateway de pagamento
        
        // Gerar código de transação
        $codigoTransacao = 'TRX' . rand(10000000, 99999999);
        
        return response()->json([
            'success' => true,
            'codigoTransacao' => $codigoTransacao,
            'codigoEnvio' => $request->codigoEnvio,
            'valorPago' => $request->valorTotal,
            'metodoPagamento' => $request->metodo,
            'message' => 'Pagamento processado com sucesso.',
            'nextStep' => 'etiqueta'
        ]);
    }
    
    /**
     * Busca informações de rastreamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarRastreamento(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
        ]);
        
        // Simulação de rastreamento
        // Em produção, aqui seria feita a integração com a API da DHL
        
        // Gerar histórico de eventos de forma aleatória
        $eventos = $this->gerarEventosRastreamento($request->codigo);
        
        return response()->json([
            'success' => true,
            'codigo' => $request->codigo,
            'origem' => 'São Paulo, Brasil',
            'destino' => 'Miami, Estados Unidos',
            'dataPostagem' => '2023-10-15',
            'status' => $eventos[0]['status'],
            'eventos' => $eventos
        ]);
    }
    
    /**
     * Gera eventos simulados de rastreamento.
     *
     * @param  string  $codigo
     * @return array
     */
    private function gerarEventosRastreamento($codigo)
    {
        $eventos = [];
        $statusOptions = [
            'Objeto postado',
            'Em trânsito',
            'Saiu para entrega',
            'Entregue ao destinatário',
            'Aguardando retirada',
            'Em processo de desembaraço',
            'Pagamento de taxa necessário'
        ];
        
        $locaisOptions = [
            'São Paulo, Brasil',
            'Rio de Janeiro, Brasil',
            'Miami, Estados Unidos',
            'Nova York, Estados Unidos',
            'Frankfurt, Alemanha',
            'Londres, Reino Unido',
            'Tóquio, Japão'
        ];
        
        // Data base (hoje menos alguns dias)
        $dataBase = strtotime('-10 days');
        
        // Número aleatório de eventos (entre 3 e 7)
        $numEventos = rand(3, 7);
        
        for ($i = 0; $i < $numEventos; $i++) {
            // Data do evento (incrementa alguns dias a cada evento)
            $dataEvento = date('Y-m-d H:i:s', $dataBase + ($i * rand(8, 24) * 3600));
            
            // Status para esse evento (último evento tem 50% de chance de ser entrega)
            $status = ($i === $numEventos - 1 && rand(0, 1) === 1) 
                ? 'Entregue ao destinatário' 
                : $statusOptions[array_rand($statusOptions)];
            
            // Local do evento
            $local = $locaisOptions[array_rand($locaisOptions)];
            
            // Adicionar evento ao array
            $eventos[] = [
                'data' => $dataEvento,
                'status' => $status,
                'local' => $local,
                'detalhe' => $this->gerarDetalheEvento($status)
            ];
        }
        
        // Ordenar eventos por data (mais recente primeiro)
        usort($eventos, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });
        
        return $eventos;
    }
    
    /**
     * Gera detalhes para o evento de rastreamento.
     *
     * @param  string  $status
     * @return string
     */
    private function gerarDetalheEvento($status)
    {
        $detalhes = [
            'Objeto postado' => 'Objeto postado pelo remetente',
            'Em trânsito' => 'Objeto em trânsito para o destino',
            'Saiu para entrega' => 'Objeto saiu para entrega ao destinatário',
            'Entregue ao destinatário' => 'Objeto entregue ao destinatário',
            'Aguardando retirada' => 'Objeto disponível para retirada em unidade',
            'Em processo de desembaraço' => 'Objeto em processo de desembaraço alfandegário',
            'Pagamento de taxa necessário' => 'Pagamento de taxa alfandegária necessário para liberação'
        ];
        
        return $detalhes[$status] ?? 'Evento registrado no sistema';
    }
    
    /**
     * Atualiza os dados do perfil do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function atualizarPerfil(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cpf' => 'required|string|max:14',
            'telefone' => 'required|string|max:20',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'cep' => 'required|string|max:10',
        ]);
        
        // Em produção, aqui seria feita a atualização no banco de dados
        
        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'usuario' => $request->all()
        ]);
    }
} 