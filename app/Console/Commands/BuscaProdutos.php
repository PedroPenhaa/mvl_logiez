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

dd(array_slice($produtos, 0, 50));
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
