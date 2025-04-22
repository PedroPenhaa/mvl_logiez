<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FedexService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DebugCotacao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:cotacao {origem?} {destino?} {peso?} {comprimento?} {largura?} {altura?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Depura a cotação da FedEx com vários parâmetros para verificar o erro';

    protected $fedexService;

    /**
     * Create a new command instance.
     */
    public function __construct(FedexService $fedexService)
    {
        parent::__construct();
        $this->fedexService = $fedexService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $origem = $this->argument('origem') ?? '01310-100'; // São Paulo
        $destino = $this->argument('destino') ?? '10001'; // New York
        $peso = $this->argument('peso') ?? 1; // kg
        $comprimento = $this->argument('comprimento') ?? 30; // cm
        $largura = $this->argument('largura') ?? 20; // cm
        $altura = $this->argument('altura') ?? 10; // cm

        $this->info('Depurando cotação com os seguintes parâmetros:');
        $this->info("Origem: $origem");
        $this->info("Destino: $destino");
        $this->info("Peso: $peso kg");
        $this->info("Dimensões: ${comprimento}x${largura}x${altura} cm");
        
        // Testar com simulação forçada
        $this->info("\nTestando com simulação forçada:");
        $resultadoSimulado = $this->fedexService->calcularCotacao(
            $origem,
            $destino,
            $altura,
            $largura,
            $comprimento,
            $peso,
            true
        );
        
        if (!empty($resultadoSimulado['cotacoesFedEx'])) {
            $this->info("✅ Simulação: " . count($resultadoSimulado['cotacoesFedEx']) . " opções de envio encontradas");
        } else {
            $this->error("❌ Simulação: Nenhuma opção de envio encontrada");
        }
        
        // Vamos tentar com vários países de destino para diagnóstico
        $this->info("\nTestando múltiplos destinos para identificar padrões:");
        
        $destinosTeste = [
            '10001' => 'EUA - New York',
            '90210' => 'EUA - Beverly Hills',
            'W1T 1DB' => 'Reino Unido - Londres',
            'H2X 1Y4' => 'Canadá - Montreal',
            '75001' => 'França - Paris',
            '28001' => 'Espanha - Madrid',
            '20010' => 'Itália - Milão',
            '1000' => 'Bélgica - Bruxelas',
            '4000' => 'Suíça - Basileia',
            '80331' => 'Alemanha - Munique',
            '1050' => 'Áustria - Viena',
            '2000' => 'Austrália - Sydney',
            '100-0001' => 'Japão - Tóquio',
            '190000' => 'Rússia - São Petersburgo',
            '110001' => 'Índia - Nova Delhi',
            '01000' => 'México - Cidade do México'
        ];
        
        $resultados = [];
        
        foreach ($destinosTeste as $cep => $descricao) {
            try {
                $this->info("Testando para $descricao ($cep)...");
                $resultado = $this->fedexService->calcularCotacao(
                    $origem,
                    $cep,
                    $altura,
                    $largura,
                    $comprimento,
                    $peso,
                    true
                );
                
                $temOpcoes = !empty($resultado['cotacoesFedEx']);
                $resultados[$cep] = [
                    'pais' => $descricao,
                    'sucesso' => $temOpcoes,
                    'qtd_opcoes' => $temOpcoes ? count($resultado['cotacoesFedEx']) : 0,
                    'erro' => $temOpcoes ? null : ($resultado['mensagem'] ?? 'Sem opções de envio')
                ];
                
                if ($temOpcoes) {
                    $this->info("  ✅ " . count($resultado['cotacoesFedEx']) . " opções encontradas");
                } else {
                    $this->error("  ❌ Nenhuma opção encontrada: " . ($resultado['mensagem'] ?? 'Sem mensagem de erro'));
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Erro: " . $e->getMessage());
                $resultados[$cep] = [
                    'pais' => $descricao,
                    'sucesso' => false,
                    'qtd_opcoes' => 0,
                    'erro' => $e->getMessage()
                ];
            }
        }
        
        // Exibir resultados
        $this->info("\nResultados consolidados:");
        $this->table(
            ['Destino', 'CEP/Código Postal', 'Status', 'Opções de Envio', 'Erro'],
            collect($resultados)->map(function ($item, $cep) {
                return [
                    $item['pais'],
                    $cep,
                    $item['sucesso'] ? '✅ Sucesso' : '❌ Falha',
                    $item['qtd_opcoes'],
                    $item['erro'] ?? '-'
                ];
            })->toArray()
        );
        
        // Análise e solução sugerida
        $this->info("\nAnálise e solução sugerida:");
        
        $sucessos = collect($resultados)->filter(function ($item) {
            return $item['sucesso'];
        })->count();
        
        $falhas = count($resultados) - $sucessos;
        
        if ($falhas == 0) {
            $this->info("Todas as cotações foram bem-sucedidas na simulação. O problema pode estar ocorrendo:");
            $this->info("1. Na API real (não na simulação)");
            $this->info("2. Com parâmetros específicos diferentes dos testados");
            $this->info("3. Na interface de usuário, não processando corretamente as respostas");
        } elseif ($sucessos == 0) {
            $this->error("Todas as cotações falharam. Possíveis problemas:");
            $this->info("1. Há um problema global na implementação da simulação de cotação");
            $this->info("2. Os parâmetros fornecidos são inválidos para todos os destinos");
            $this->info("3. O formato dos CEPs/códigos postais pode estar inconsistente");
        } else {
            $this->info("Algumas cotações funcionaram e outras não. Isso sugere:");
            $this->info("1. A FedEx pode não oferecer serviços para determinados países/regiões");
            $this->info("2. O formato do CEP/código postal pode estar correto apenas para alguns países");
            $this->info("3. Pode haver restrições de dimensões ou peso para alguns destinos");
        }
        
        // Verificar se há problemas no destino específico mencionado pelo usuário
        if (isset($destinosTeste[$destino])) {
            $resultadoDestino = $resultados[$destino];
            if (!$resultadoDestino['sucesso']) {
                $this->error("\nO destino específico que você está tentando ($destino) falhou com o erro: " . $resultadoDestino['erro']);
                $this->info("Isso confirma que o problema está relacionado a este destino específico.");
            } else {
                $this->info("\nO destino específico que você está tentando ($destino) funcionou corretamente com " . $resultadoDestino['qtd_opcoes'] . " opções.");
                $this->info("Isso sugere que o problema pode estar em outro lugar, não na cotação em si.");
            }
        }
        
        return Command::SUCCESS;
    }
} 