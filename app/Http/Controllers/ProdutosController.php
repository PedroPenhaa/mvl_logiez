<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProdutosController extends Controller
{
    /**
     * Retorna a lista de produtos do arquivo JSON com paginação e filtro
     */
    public function getProdutos(Request $request)
    {
        $jsonFilePath = storage_path('app/produtos_receita.json');
        
        if (!File::exists($jsonFilePath)) {
            Log::error('Arquivo de produtos não encontrado: ' . $jsonFilePath);
            return response()->json([
                'error' => 'Arquivo de produtos não encontrado',
                'path' => $jsonFilePath
            ], 404);
        }
        
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
            
            Log::info('Requisição de produtos recebida', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search
            ]);
            
            $conteudo = File::get($jsonFilePath);
            $todosProdutos = json_decode($conteudo, true);
            
            if (!is_array($todosProdutos)) {
                Log::error('Erro ao decodificar JSON', [
                    'conteudo' => substr($conteudo, 0, 100) . '...',
                    'erro' => json_last_error_msg()
                ]);
                
                return response()->json([
                    'error' => 'Erro ao decodificar JSON',
                    'message' => 'O arquivo não contém JSON válido: ' . json_last_error_msg()
                ], 500);
            }
            
            Log::info('Total de produtos no arquivo: ' . count($todosProdutos));
            
            // Aplicar filtro de busca se fornecido
            if (!empty($search)) {
                // Verificar se a busca é um objeto com descrição e código ou uma string simples
                $buscaPorDescricao = is_array($search) && isset($search['descricao']) ? $search['descricao'] : '';
                $buscaPorCodigo = is_array($search) && isset($search['codigo']) ? $search['codigo'] : '';
                
                // Se a busca for uma string simples, buscar em ambos os campos
                if (!is_array($search)) {
                    $buscaPorDescricao = $buscaPorCodigo = $search;
                }
                
                Log::info('Filtrando produtos', [
                    'busca_descricao' => $buscaPorDescricao,
                    'busca_ncm' => $buscaPorCodigo
                ]);
                
                $todosProdutos = array_filter($todosProdutos, function($produto) use ($buscaPorDescricao, $buscaPorCodigo) {
                    $matchDescricao = empty($buscaPorDescricao) || stripos($produto['descricao'], $buscaPorDescricao) !== false;
                    $matchCodigo = empty($buscaPorCodigo) || stripos($produto['codigo'], $buscaPorCodigo) !== false;
                    
                    // Retornar true se ambos os filtros aplicáveis encontrarem correspondência
                    return $matchDescricao && $matchCodigo;
                });
                
                // Reindexar array após filtro
                $todosProdutos = array_values($todosProdutos);
                Log::info('Produtos filtrados: ' . count($todosProdutos));
            }
            
            // Calcular total de produtos após aplicar filtro
            $total = count($todosProdutos);
            
            // Aplicar paginação
            $offset = ($page - 1) * $limit;
            $produtos = array_slice($todosProdutos, $offset, $limit);
            
            Log::info('Retornando produtos', [
                'pagina' => $page,
                'de' => $offset + 1,
                'ate' => $offset + count($produtos),
                'total' => $total,
                'qtd_retornada' => count($produtos)
            ]);
            
            return response()->json([
                'produtos' => $produtos,
                'total' => $total,
                'page' => (int)$page,
                'limit' => (int)$limit,
                'totalPages' => ceil($total / $limit)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao ler arquivo de produtos', [
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao ler o arquivo de produtos',
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
} 