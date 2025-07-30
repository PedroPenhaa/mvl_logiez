<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultaGemini extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consulta:gemini 
                            {--produto= : Nome do produto para consultar o NCM}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta o NCM de um produto usando a API do Gemini';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Consulta de NCM de produtos via API Gemini');
        $this->line('----------------------------------------');

        // Verificar se o produto foi passado como parâmetro
        $produto = $this->option('produto');
        
        // Se o produto não for passado como parâmetro e o modo interativo estiver ativo, solicitar ao usuário
        if (empty($produto) && $this->input->isInteractive()) {
            $produto = $this->ask('Digite o nome do produto para consultar o NCM');
        }

        if (empty($produto)) {
            $this->error('Nome do produto não pode ser vazio!');
            return Command::FAILURE;
        }

        $this->info('Consultando NCM para o produto: ' . $produto);
        
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
            
            // Preparar a requisição
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($endpoint . '?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Qual o NCM deste produto: $produto? Por favor, forneça apenas o código NCM e uma breve descrição."
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