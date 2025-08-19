<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Models\Produto;
use App\Models\UnidadeTributaria;

class ProdutosController extends Controller
{
    /**
     * Retorna a lista de produtos baseada na consulta do Gemini
     */
    public function getProdutos(Request $request)
    {
        try {
            // Parâmetros da requisição
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 100);
            $search = $request->input('search', '');
            
            // Se o search for uma string JSON, decodificar
            if (is_string($search) && !empty($search) && $search !== '') {
                if ($this->isJson($search)) {
                    $search = json_decode($search, true);
                }
            }
            
            // Extrair informações da busca
            $buscaPorDescricao = is_array($search) && isset($search['descricao']) ? $search['descricao'] : '';
            $buscaPorCodigo = is_array($search) && isset($search['codigo']) ? $search['codigo'] : '';
            
            // Se não houver busca específica, retornar lista vazia
            if (empty($buscaPorDescricao) && empty($buscaPorCodigo)) {
                return response()->json([
                    'produtos' => [],
                    'total' => 0,
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'totalPages' => 0
                ]);
            }
            
            // Criar um produto baseado na busca
            $produto = [
                'codigo' => $buscaPorCodigo ?: '0000.00.00',
                'descricao' => $buscaPorDescricao ?: 'Produto não especificado',
                'valor' => 10.00 // Valor padrão
            ];
            
            return response()->json([
                'produtos' => [$produto],
                'total' => 1,
                'page' => (int)$page,
                'limit' => (int)$limit,
                'totalPages' => 1
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao processar busca de produtos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica se uma string é um JSON válido
     * 
     * @param string $string A string a ser verificada
     * @return bool Retorna true se for um JSON válido, false caso contrário
     */
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    /**
     * Consulta o NCM de um produto via Gemini
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function consultarGemini(Request $request)
    {
        $request->validate([
            'produto' => 'required|string|min:2|max:255'
        ]);
        
        $produto = $request->input('produto');
        
        try {
            // Usar o comando Artisan que já funciona
            $result = Artisan::call('consulta:gemini', [
                'produto' => $produto
            ]);
            
            $output = Artisan::output();
            
            // Decodificar a resposta JSON do comando
            $data = json_decode($output, true);
            
            if (!$data || !isset($data['success'])) {
                throw new \Exception('Resposta inválida do comando Gemini');
            }
            
            if (!$data['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $data['error'] ?? 'Erro na consulta'
                ], 400);
            }
            
            // Extrair NCM, descrição e unidade da resposta
            $ncm = $data['ncm'] ?? '';
            $descricao = $data['descricao'] ?? $produto;
            $unidade = $data['unidade'] ?? 'UN';
            
            return response()->json([
                'success' => true,
                'ncm' => $ncm,
                'descricao' => $descricao,
                'unidade' => $unidade,
                'produto_original' => $produto
            ]);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'error' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Consulta a unidade tributária de um NCM no arquivo CSV
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function consultarUnidadeTributaria(Request $request)
    {
        $ncm = $request->input('ncm');
        
        if (empty($ncm)) {
            return response()->json([
                'success' => false,
                'error' => 'NCM não informado'
            ], 400);
        }
        
        try {
            // Formatar o NCM: remover pontos e zeros à esquerda
            $ncmFormatado = preg_replace('/^0+/', '', str_replace('.', '', $ncm));
            
            $caminhoArquivo = storage_path('app/Unidade_trib.csv');
            
            if (!file_exists($caminhoArquivo)) {
                
                return response()->json([
                    'success' => false,
                    'error' => 'Arquivo de unidades não encontrado'
                ], 404);
            }
            
            // Verificar se o arquivo está acessível e ler as primeiras linhas para depuração
            try {
                $primeiraLinhas = [];
                $handle = fopen($caminhoArquivo, 'r');
                for ($i = 0; $i < 5; $i++) {
                    if (($linha = fgetcsv($handle)) !== false) {
                        $primeiraLinhas[] = $linha;
                    }
                }
                fclose($handle);
                
            } catch (\Exception $e) {
            }
            
            // Abrir o arquivo CSV
            $arquivo = fopen($caminhoArquivo, 'r');
            
            // Pular a primeira linha (cabeçalho)
            fgetcsv($arquivo, 0, ',');
            
            $unidade = null;
            $nomeUnidade = null;
            $linhasVerificadas = 0;
            $linhasEncontradas = [];
            
            // Percorrer o arquivo linha por linha
            while (($linha = fgetcsv($arquivo, 0, ',')) !== FALSE) {
                $linhasVerificadas++;
                
                // Para depuração, salvar algumas linhas que contenham parte do NCM
                if (strpos($linha[0], substr($ncmFormatado, 0, 3)) === 0 && count($linhasEncontradas) < 5) {
                    $linhasEncontradas[] = $linha;
                }
                
                // Verificar se o NCM da linha corresponde ao NCM da consulta formatado
                if ($linha[0] == $ncmFormatado) {
                    $unidade = $linha[1]; // Coluna 1 é a unidade (abreviatura)
                    $nomeUnidade = $linha[2]; // Coluna 2 é o nome completo da unidade
                    break;
                }
            }
            
            // Fechar o arquivo
            fclose($arquivo);
            
            if ($unidade) {
                return response()->json([
                    'success' => true,
                    'unidade' => $unidade,
                    'nome_unidade' => $nomeUnidade
                ]);
            } else {
                
                return response()->json([
                    'success' => false,
                    'error' => 'Unidade não encontrada para o NCM: ' . $ncm
                ]);
            }
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao consultar unidade: ' . $e->getMessage()
            ], 500);
        }
    }
} 