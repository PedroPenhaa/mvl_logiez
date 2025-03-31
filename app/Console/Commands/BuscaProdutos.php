<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BuscaProdutos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'busca:produtos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca produtos da nomenclatura SISCOMEX';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando busca de produtos da nomenclatura SISCOMEX...');

        // URL da API
        $url = "https://portalunico.siscomex.gov.br/classif/api/publico/nomenclatura/download/json?perfil=PUBLICO";

        // Definir o nome do arquivo para salvar os dados
        $outputPath = 'nomenclatura_' . date('Y-m-d') . '.json';

        $this->info('Fazendo download dos dados...');

        try {
            // Inicializa cURL
            $ch = curl_init();

            // Configura opções do cURL
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false, // Apenas para desenvolvimento
                CURLOPT_TIMEOUT => 180, // 3 minutos para completar o download
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ]);

            // Executa a requisição
            $response = curl_exec($ch);

            // Verifica se houve erro
            if (curl_errno($ch)) {
                throw new \Exception('Erro ao fazer download: ' . curl_error($ch));
            }

            // Verifica o código de status HTTP
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                throw new \Exception('API retornou código HTTP ' . $httpCode);
            }

            // Fecha a sessão cURL
            curl_close($ch);

            // Verificar se a resposta é um JSON válido
            // Primeira decodificação
            $decoded = json_decode($response, true);


        dd($decoded);

            // Se o decode direto não retornar o array esperado, tenta fazer outro decode no campo "data"
            if (isset($decoded['data']) && is_string($decoded['data'])) {
                $innerData = json_decode($decoded['data'], true);
            } else {
                $innerData = $decoded;
            }

            // Validação do campo "Nomenclaturas"
            if (!isset($innerData['Nomenclaturas'])) {
                throw new \Exception('Campo "Nomenclaturas" não encontrado.');
            }

            // Monta o array de produtos
            $produtos = array_map(function ($item) {
                return [
                    'codigo' => $item['Codigo'],
                    'descricao' => $item['Descricao'],
                ];
            }, $innerData['Nomenclaturas']);

            // Exibir resumo no console
            $totalItems = count($produtos);
            $this->info("Download concluído com sucesso!");
            $this->info("Total de itens encontrados: {$totalItems}");

            // Debug: mostrar um exemplo
            $this->info('Exemplo de produto:');
            $this->line(print_r($produtos[0], true));

            // Salvar os produtos em um arquivo txt
            $this->info('Salvando produtos em arquivo texto...');
            
            // Criar conteúdo formatado para o arquivo txt
            $conteudo = "Lista de Produtos SISCOMEX\n";
            $conteudo .= "=======================\n\n";
            
            foreach ($produtos as $index => $produto) {
                $conteudo .= "[{$index}] Código: {$produto['codigo']}\n";
                $conteudo .= "     Descrição: {$produto['descricao']}\n\n";
            }
            
            // Definir o caminho do arquivo
            $txtFilePath = storage_path('app/produtos_receita' . '.txt');
            
            // Salvar o arquivo
            file_put_contents($txtFilePath, $conteudo);
            $this->info("Arquivo txt criado com sucesso em: {$txtFilePath}");

            //Formatando os caracteres especiais antes de salvar
            $this->info('Tratando caracteres especiais e formatando descrições...');
            
            // Array para armazenar produtos sem repetição de descrição
            $produtosUnicos = [];
            $descricoesVistas = [];

            dd($produtos);
            
            foreach ($produtos as &$produto) {
                // 1. Decodifica os caracteres unicode na descrição
                $produto['descricao'] = json_decode('"' . $produto['descricao'] . '"');
                
                // 2. Remove os traços (-- ou -) no início das descrições
                $produto['descricao'] = preg_replace('/^--?\s+/', '', $produto['descricao']);
                
                // 3. Remove tags HTML
                $produto['descricao'] = strip_tags($produto['descricao']);
            }


            
            // 4. Filtrar produtos para eliminar repetições nas descrições
            foreach ($produtos as $produto) {
                if (!in_array($produto['descricao'], $descricoesVistas)) {
                    $descricoesVistas[] = $produto['descricao'];
                    $produtosUnicos[] = $produto;
                }
            }
            
            $this->info('Total de produtos após remoção de duplicidades: ' . count($produtosUnicos));

            //quero que salve todos os produtos no retorno.
            $this->info('Salvando produtos em arquivo json...');
            $jsonFilePath = storage_path('app/produtos_receita.json');
            File::put($jsonFilePath, json_encode($produtosUnicos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("Arquivo json criado com sucesso em: {$jsonFilePath}");

       
            // Opcional: salvar o resultado filtrado
            //Storage::disk('local')->put('produtos_filtrados.json', json_encode($produtos, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
            Log::error('Erro ao buscar produtos SISCOMEX: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
