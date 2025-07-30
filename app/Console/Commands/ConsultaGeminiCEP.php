<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultaGeminiCEP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consulta:gemini-cep 
                            {--cep= : CEP para consultar o endereço}
                            {--endereco= : Endereço completo (rua, cidade, estado, país) para consultar o CEP}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta CEP ou endereço usando a API do Gemini';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Consulta de CEP/Endereço via API Gemini');
        $this->line('----------------------------------------');

        // Verificar se CEP ou endereço foi passado como parâmetro
        $cep = $this->option('cep');
        $endereco = $this->option('endereco');
        
        // Se nenhum parâmetro foi passado e o modo interativo estiver ativo, solicitar ao usuário
        if (empty($cep) && empty($endereco) && $this->input->isInteractive()) {
            $tipo = $this->choice('Que tipo de consulta você deseja fazer?', [
                'cep' => 'Consultar endereço por CEP',
                'endereco' => 'Consultar CEP por endereço'
            ]);
            
            if ($tipo === 'cep') {
                $cep = $this->ask('Digite o CEP para consultar o endereço');
            } else {
                $endereco = $this->ask('Digite o endereço completo (rua, cidade, estado, país) para consultar o CEP');
            }
        }

        if (empty($cep) && empty($endereco)) {
            $this->error('CEP ou endereço deve ser fornecido!');
            return Command::FAILURE;
        }

        // Determinar o tipo de consulta
        $tipoConsulta = !empty($cep) ? 'cep' : 'endereco';
        $valor = !empty($cep) ? $cep : $endereco;

        $this->info('Consultando ' . ($tipoConsulta === 'cep' ? 'endereço para o CEP' : 'CEP para o endereço') . ': ' . $valor);
        
        try {
            // Obter chave da API do Gemini
            $apiKey = config('services.gemini.api_key');
            
            // Se não estiver configurada, solicitar ao usuário
            if (empty($apiKey)) {
                $this->warn('Chave da API do Gemini não encontrada no arquivo de configuração.');
                $apiKey = $this->secret('Digite sua chave de API do Gemini');
                
                if (empty($apiKey)) {
                    $this->error('A chave de API é obrigatória para usar este comando.');
                    return Command::FAILURE;
                }
            }
            
            // Atualizado para usar o modelo correto
            $model = config('services.gemini.model', 'gemini-2.0-flash');
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
            
            // Preparar a pergunta baseada no tipo de consulta
            if ($tipoConsulta === 'cep') {
                $pergunta = "Para o CEP $cep, forneça as seguintes informações: País, Estado, Cidade e Rua. Responda em formato JSON com as chaves: pais, estado, cidade, rua.";
            } else {
                $pergunta = "Para o endereço: $endereco, forneça o CEP correspondente. Responda apenas com o CEP no formato 00000-000.";
            }
            
            // Preparar a requisição
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($endpoint . '?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $pergunta
                            ]
                        ]
                    ]
                ]
            ]);
            
            // Verificar se a requisição foi bem-sucedida
            if ($response->successful()) {
                $data = $response->json();
                
                // Debug de resposta
                $this->line('Resposta recebida: ' . json_encode($data, JSON_PRETTY_PRINT));
                
                // Extrair a resposta do Gemini
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $result = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Exibir o resultado formatado
                    $this->newLine();
                    $this->info('Resultado da consulta:');
                    $this->line('-------------------');
                    $this->line($result);
                    $this->newLine();
                } else {
                    $this->error('Formato de resposta inesperado da API Gemini.');
                    $this->line('Resposta completa: ' . json_encode($data));
                }
            } else {
                $this->error('Erro ao consultar a API Gemini: ' . $response->status());
                $this->line('Detalhes: ' . $response->body());
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro ao processar a consulta: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 