<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    /**
     * Lista os produtos da nomenclatura SISCOMEX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Tenta buscar os produtos do cache
        $produtos = Cache::get('siscomex_produtos');
        
        // Se não estiver no cache, tenta buscar do arquivo JSON de backup
        if (!$produtos && Storage::disk('local')->exists('produtos_siscomex.json')) {
            $jsonContent = Storage::disk('local')->get('produtos_siscomex.json');
            $produtos = json_decode($jsonContent, true);
        }
        
        // Se não encontrar produtos, retorna uma resposta vazia
        if (!$produtos) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum produto encontrado. Execute o comando "busca:produtos" para sincronizar os dados.',
                'data' => []
            ]);
        }
        
        // Implementa filtros básicos (se fornecidos)
        $filtro = $request->query('filtro');
        $pagina = (int)$request->query('pagina', 1);
        $porPagina = (int)$request->query('por_pagina', 50);
        
        // Filtra os produtos se um filtro for especificado
        if ($filtro) {
            $filtro = strtolower($filtro);
            $produtos = array_filter($produtos, function($produto) use ($filtro) {
                return str_contains(strtolower($produto['codigo']), $filtro) || 
                       str_contains(strtolower($produto['descricao']), $filtro);
            });
        }
        
        // Ordenar produtos por código
        usort($produtos, function($a, $b) {
            return $a['codigo'] <=> $b['codigo'];
        });
        
        // Calcula a paginação
        $total = count($produtos);
        $totalPaginas = ceil($total / $porPagina);
        
        // Ajusta a página para estar dentro dos limites
        $pagina = max(1, min($pagina, $totalPaginas));
        
        // Extrai os produtos da página atual
        $inicio = ($pagina - 1) * $porPagina;
        $produtosPaginados = array_slice($produtos, $inicio, $porPagina);
        
        return response()->json([
            'success' => true,
            'total' => $total,
            'por_pagina' => $porPagina,
            'pagina_atual' => $pagina,
            'total_paginas' => $totalPaginas,
            'data' => $produtosPaginados
        ]);
    }
    
    /**
     * Busca um produto específico pelo código.
     *
     * @param  string  $codigo
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($codigo)
    {
        // Tenta buscar os produtos do cache
        $produtos = Cache::get('siscomex_produtos');
        
        // Se não estiver no cache, tenta buscar do arquivo JSON de backup
        if (!$produtos && Storage::disk('local')->exists('produtos_siscomex.json')) {
            $jsonContent = Storage::disk('local')->get('produtos_siscomex.json');
            $produtos = json_decode($jsonContent, true);
        }
        
        // Se não encontrar produtos, retorna erro
        if (!$produtos) {
            return response()->json([
                'success' => false,
                'message' => 'Base de produtos não disponível. Execute o comando "busca:produtos" para sincronizar os dados.'
            ], 404);
        }
        
        // Busca o produto pelo código
        $produto = null;
        foreach ($produtos as $item) {
            if ($item['codigo'] === $codigo) {
                $produto = $item;
                break;
            }
        }
        
        if (!$produto) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $produto
        ]);
    }
} 