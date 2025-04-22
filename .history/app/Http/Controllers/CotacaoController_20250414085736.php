<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FedexService;
use Illuminate\Support\Facades\Log;

class CotacaoController extends Controller
{
    protected $fedexService;
    
    public function __construct(FedexService $fedexService)
    {
        $this->fedexService = $fedexService;
    }
    
    public function index()
    {
        return view('cotacao.index');
    }
    
    public function calcular(Request $request)
    {
        // Validar os dados de entrada
        $validated = $request->validate([
            'origem' => 'required',
            'destino' => 'required',
            'peso' => 'required|numeric',
            'comprimento' => 'required|numeric',
            'largura' => 'required|numeric',
            'altura' => 'required|numeric',
        ]);
        
        try {
            // Registrar o que está sendo enviado para facilitar diagnóstico
            \Log::info('Tentativa de cotação:', [
                'parametros' => $request->all(),
                'timestamp' => now()->toDateTimeString()
            ]);
            
            try {
                // Primeiro tentar usar a API real
                $resultado = $this->fedexService->calcularCotacao(
                    $request->origem,
                    $request->destino,
                    $request->altura,
                    $request->largura,
                    $request->comprimento,
                    $request->peso,
                    false // Tentar usar API real
                );
                
                \Log::info('Resposta da API real da FedEx:', [
                    'sucesso' => $resultado['success'] ?? false,
                    'opcoes' => count($resultado['cotacoesFedEx'] ?? []),
                    'mensagem' => $resultado['mensagem'] ?? 'Sem mensagem'
                ]);
            } catch (\Exception $e) {
                // Se falhar, registrar o erro e usar simulação como fallback
                \Log::warning('Erro na API real, usando simulação como fallback:', [
                    'erro' => $e->getMessage(),
                    'parametros' => $request->all()
                ]);
                
                // Usar simulação como fallback
                $resultado = $this->fedexService->calcularCotacao(
                    $request->origem,
                    $request->destino,
                    $request->altura,
                    $request->largura,
                    $request->comprimento,
                    $request->peso,
                    true // Forçar simulação
                );
                
                // Adicionar indicação que foi usada simulação por fallback
                $resultado['usou_fallback'] = true;
                
                \Log::info('Resposta da simulação (fallback):', [
                    'sucesso' => $resultado['success'] ?? false,
                    'opcoes' => count($resultado['cotacoesFedEx'] ?? []),
                    'mensagem' => $resultado['mensagem'] ?? 'Sem mensagem'
                ]);
            }
            
            // Verificar se há opções de envio disponíveis
            if (empty($resultado['cotacoesFedEx'])) {
                // Se não houver opções, retornar uma mensagem mais clara
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nenhuma opção de envio encontrada para os parâmetros fornecidos.',
                    'detalhes' => 'Verifique se os CEPs de origem e destino estão corretos e se as dimensões e peso estão dentro dos limites aceitos.',
                    'data_calculo' => now()->toDateTimeString()
                ]);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $resultado,
                'data_calculo' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro crítico na cotação:', [
                'mensagem' => $e->getMessage(),
                'parametros' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao calcular cotação: ' . $e->getMessage(),
                'data_calculo' => now()->toDateTimeString()
            ], 500);
        }
    }
} 