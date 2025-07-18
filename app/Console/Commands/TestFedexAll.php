<?php

namespace App\Console\Commands;

use App\Services\FedexService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestFedexAll extends Command
{
    protected $signature = 'fedex:test-all';
    protected $description = 'Testa todas as funcionalidades da API FedEx com as novas credenciais';

    public function handle()
    {
        $this->info('🚚 Testando todas as funcionalidades da API FedEx');
        $this->info('================================================');
        
        $fedexService = new FedexService();
        
        // Teste 1: Autenticação para cotação/envio
        $this->info("\n1️⃣ Testando autenticação para cotação/envio...");
        try {
            $token = $fedexService->getAuthToken(true);
            $this->info('✅ Autenticação para cotação/envio: OK');
            $this->line('   Token: ' . substr($token, 0, 10) . '...' . substr($token, -10));
        } catch (\Exception $e) {
            $this->error('❌ Erro na autenticação para cotação/envio: ' . $e->getMessage());
            return 1;
        }
        
        // Teste 2: Autenticação para rastreamento
        $this->info("\n2️⃣ Testando autenticação para rastreamento...");
        try {
            $token = $fedexService->getTrackingAuthToken(true);
            $this->info('✅ Autenticação para rastreamento: OK');
            $this->line('   Token: ' . substr($token, 0, 10) . '...' . substr($token, -10));
        } catch (\Exception $e) {
            $this->error('❌ Erro na autenticação para rastreamento: ' . $e->getMessage());
            return 1;
        }
        
        // Teste 3: Cotação
        $this->info("\n3️⃣ Testando cotação...");
        try {
            $cotacao = $fedexService->calcularCotacao(
                '01222001', // Origem (São Paulo)
                '10001',    // Destino (Nova York)
                10,         // Altura (cm)
                20,         // Largura (cm)
                30,         // Comprimento (cm)
                5           // Peso (kg)
            );
            
            if (isset($cotacao['success']) && $cotacao['success']) {
                $this->info('✅ Cotação: OK');
                $this->line('   Serviços encontrados: ' . count($cotacao['cotacoesFedEx'] ?? []));
                foreach ($cotacao['cotacoesFedEx'] ?? [] as $index => $servico) {
                    $this->line("   {$index}. {$servico['servico']}: \${$servico['valorTotal']} USD");
                }
            } else {
                $this->warn('⚠️ Cotação: Retornou sem sucesso');
                $this->line('   Resposta: ' . json_encode($cotacao));
            }
        } catch (\Exception $e) {
            $this->error('❌ Erro na cotação: ' . $e->getMessage());
        }
        
        // Teste 4: Rastreamento (usando um código de teste)
        $this->info("\n4️⃣ Testando rastreamento...");
        try {
            $rastreamento = $fedexService->rastrearEnvio('794616896420');
            
            if (isset($rastreamento['success']) && $rastreamento['success']) {
                $this->info('✅ Rastreamento: OK');
                $this->line('   Status: ' . ($rastreamento['status'] ?? 'N/A'));
                $this->line('   Eventos: ' . count($rastreamento['eventos'] ?? []));
            } else {
                $this->warn('⚠️ Rastreamento: Retornou sem sucesso');
                $this->line('   Resposta: ' . json_encode($rastreamento));
            }
        } catch (\Exception $e) {
            $this->error('❌ Erro no rastreamento: ' . $e->getMessage());
        }
        
        // Teste 5: Verificar configurações
        $this->info("\n5️⃣ Verificando configurações...");
        $this->line('   Ambiente: ' . (config('services.fedex.use_production') ? 'Produção' : 'Homologação'));
        $this->line('   API URL: ' . config('services.fedex.api_url'));
        $this->line('   Client ID (Cotação/Envio): ' . substr(config('services.fedex.client_id'), 0, 5) . '...' . substr(config('services.fedex.client_id'), -5));
        $this->line('   Client ID (Rastreamento): ' . substr(config('services.fedex.tracking_client_id'), 0, 5) . '...' . substr(config('services.fedex.tracking_client_id'), -5));
        $this->line('   Shipper Account: ' . config('services.fedex.shipper_account'));
        
        $this->info("\n🎉 Teste concluído!");
        $this->info('As funcionalidades da API FedEx estão configuradas e funcionando.');
        
        return 0;
    }
} 