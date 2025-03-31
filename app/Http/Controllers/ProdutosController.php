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
        // Validar o request
        $request->validate([
            'produto' => 'required|string|min:2|max:255'
        ]);
        
        $produto = $request->input('produto');
        
        try {
            // Primeiro método: Usar o Kernel do Artisan diretamente (mais confiável)
            try {
                Log::info('Usando o Kernel para executar o comando ConsultaGemini', [
                    'produto' => $produto
                ]);
                
                $output = new \Symfony\Component\Console\Output\BufferedOutput;
                $exitCode = \Illuminate\Support\Facades\Artisan::call('consulta:gemini', [
                    '--produto' => $produto
                ], $output);
                
                $resultado = $output->fetch();
                
                if ($exitCode === 0) {
                    Log::info('Comando ConsultaGemini executado com sucesso via Kernel', [
                        'resultado' => $resultado
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'resultado' => $resultado,
                        'metodo' => 'kernel'
                    ]);
                }
                
                // Se não funcionou, registre o erro e tente o método alternativo
                Log::warning('Falha ao executar comando via Kernel, tentando método alternativo', [
                    'exit_code' => $exitCode,
                    'output' => $resultado
                ]);
                
            } catch (\Exception $e) {
                Log::warning('Exceção no método Kernel', [
                    'mensagem' => $e->getMessage()
                ]);
                // Continuar com o método alternativo
            }
            
            // Método alternativo: Execução via shell
            $saida = [];
            $returnCode = 0;
            
            // Usar o caminho absoluto para o arquivo artisan
            $artisanPath = base_path('artisan');
            $command = "php {$artisanPath} consulta:gemini --produto=" . escapeshellarg($produto);
            
            // Loggar o comando para diagnóstico
            Log::info('Executando comando:', [
                'comando' => $command,
                'artisan_path_exists' => file_exists($artisanPath)
            ]);
            
            // Executar o comando
            exec($command, $saida, $returnCode);
            
            // Verificar se o comando foi bem-sucedido
            if ($returnCode !== 0) {
                Log::error('Erro ao executar o comando ConsultaGemini', [
                    'produto' => $produto,
                    'saida' => $saida,
                    'codigo_retorno' => $returnCode,
                    'comando' => $command,
                    'diretorio_atual' => getcwd()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao consultar NCM via Gemini',
                    'saida' => $saida,
                    'codigo' => $returnCode,
                    'debug_info' => [
                        'artisan_path' => $artisanPath,
                        'artisan_exists' => file_exists($artisanPath),
                        'current_dir' => getcwd()
                    ]
                ], 500);
            }
            
            // Juntar a saída em uma string
            $resultado = implode("\n", $saida);
            
            // Verificar se o resultado contém informações úteis
            $resultadoUtil = false;
            foreach ($saida as $linha) {
                if (strpos($linha, 'NCM') !== false || preg_match('/\d{4}\.\d{2}/', $linha)) {
                    $resultadoUtil = true;
                    break;
                }
            }
            
            if (!$resultadoUtil) {
                Log::warning('Resultado do Gemini não parece conter um NCM', [
                    'produto' => $produto,
                    'resultado' => $resultado
                ]);
            }
            
            // Log da resposta para debug
            Log::info('Resposta do comando ConsultaGemini', [
                'produto' => $produto,
                'resultado' => $resultado
            ]);
            
            return response()->json([
                'success' => true,
                'resultado' => $resultado,
                'metodo' => 'exec'
            ]);
        } catch (\Exception $e) {
            Log::error('Exceção ao consultar NCM via Gemini', [
                'produto' => $produto,
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erro ao consultar NCM: ' . $e->getMessage()
            ], 500);
        }
    }
} 