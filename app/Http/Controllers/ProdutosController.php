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
                
                // Caso especial para o NCM de Havaianas
                if ($buscaPorCodigo === '6402.20.00') {
                    Log::info('Caso especial: busca por Havaianas NCM 6402.20.00');
                    
                    $todosProdutos = array_filter($todosProdutos, function($produto) {
                        // Verificar exatamente o NCM 6402.20.00
                        return $produto['codigo'] === '6402.20.00';
                    });
                    
                    // Reindexar array após filtro
                    $todosProdutos = array_values($todosProdutos);
                    Log::info('Produtos filtrados para Havaianas:', [
                        'quantidade' => count($todosProdutos),
                        'produtos' => $todosProdutos
                    ]);
                } 
                // Filtro normal para outros casos
                else {
                    // Se a busca for uma string simples, buscar em ambos os campos
                    if (!is_array($search)) {
                        $buscaPorDescricao = $buscaPorCodigo = $search;
                    }
                    
                    Log::info('Filtrando produtos', [
                        'busca_descricao' => $buscaPorDescricao,
                        'busca_ncm' => $buscaPorCodigo
                    ]);
                    
                    $todosProdutos = array_filter($todosProdutos, function($produto) use ($buscaPorDescricao, $buscaPorCodigo) {
                        // Verificar correspondência por descrição
                        $matchDescricao = empty($buscaPorDescricao) || stripos($produto['descricao'], $buscaPorDescricao) !== false;
                        
                        // Verificar correspondência por código NCM - com tratamento especial
                        if (!empty($buscaPorCodigo)) {
                            // Normalizar o código NCM para comparação (remover pontos)
                            $codigoProdutoNormalizado = str_replace('.', '', $produto['codigo']);
                            $buscaPorCodigoNormalizado = str_replace('.', '', $buscaPorCodigo);
                            
                            // Log detalhado para depuração
                            Log::debug('Comparando códigos NCM', [
                                'busca_original' => $buscaPorCodigo,
                                'busca_normalizado' => $buscaPorCodigoNormalizado,
                                'produto_codigo' => $produto['codigo'],
                                'produto_normalizado' => $codigoProdutoNormalizado
                            ]);
                            
                            // Para NCMs completos (8 dígitos como 6402.20.00), verificar correspondência exata
                            if (strlen($buscaPorCodigoNormalizado) >= 8) {
                                // Comparação exata do início do código (ignora subcategorias)
                                $matchCodigo = (substr($codigoProdutoNormalizado, 0, strlen($buscaPorCodigoNormalizado)) === $buscaPorCodigoNormalizado);
                                
                                // Case específico para Havaianas (6402.20.00)
                                if ($buscaPorCodigo === '6402.20.00' && $produto['codigo'] === '6402.20.00') {
                                    $matchCodigo = true;
                                    Log::info('Match exato encontrado para Havaianas', [
                                        'codigo' => $produto['codigo'],
                                        'descricao' => $produto['descricao']
                                    ]);
                                }
                            }
                            // Para NCMs parciais, verificar se o início corresponde
                            else {
                                $matchCodigo = (strpos($codigoProdutoNormalizado, $buscaPorCodigoNormalizado) === 0);
                            }
                        } else {
                            $matchCodigo = true; // Se não houver código para busca, não filtrar por código
                        }
                        
                        // Log para debug de cada comparação
                        if (!empty($buscaPorCodigo) && $matchCodigo) {
                            Log::info('Match encontrado para NCM', [
                                'busca' => $buscaPorCodigo,
                                'produto_codigo' => $produto['codigo'],
                                'produto_descricao' => $produto['descricao']
                            ]);
                        }
                        
                        // Retornar true se ambos os filtros aplicáveis encontrarem correspondência
                        return $matchDescricao && $matchCodigo;
                    });
                    
                    // Reindexar array após filtro
                    $todosProdutos = array_values($todosProdutos);
                    Log::info('Produtos filtrados: ' . count($todosProdutos));
                }
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
            Log::error('Erro na consulta Gemini: ' . $e->getMessage());
            
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
            
            Log::info('Consultando unidade tributária', [
                'ncm_original' => $ncm,
                'ncm_formatado' => $ncmFormatado
            ]);
            
            $caminhoArquivo = storage_path('app/Unidade_trib.csv');
            
            if (!file_exists($caminhoArquivo)) {
                Log::error('Arquivo Unidade_trib.csv não encontrado', [
                    'caminho' => $caminhoArquivo
                ]);
                
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
                
                Log::debug('Primeiras linhas do arquivo CSV:', [
                    'linhas' => $primeiraLinhas
                ]);
            } catch (\Exception $e) {
                Log::error('Erro ao ler amostra do arquivo:', [
                    'erro' => $e->getMessage()
                ]);
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
                    Log::info('Unidade encontrada', [
                        'ncm' => $ncmFormatado,
                        'unidade' => $unidade,
                        'nome_unidade' => $nomeUnidade
                    ]);
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
                Log::warning('Unidade não encontrada', [
                    'ncm_original' => $ncm,
                    'ncm_formatado' => $ncmFormatado,
                    'linhas_verificadas' => $linhasVerificadas,
                    'amostras_proximas' => $linhasEncontradas
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Unidade não encontrada para o NCM: ' . $ncm
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao consultar unidade tributária', [
                'ncm' => $ncm,
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao consultar unidade: ' . $e->getMessage()
            ], 500);
        }
    }
} 