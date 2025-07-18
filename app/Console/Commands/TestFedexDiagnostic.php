<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestFedexDiagnostic extends Command
{
    protected $signature = 'fedex:diagnostic';
    protected $description = 'Diagnóstico detalhado das credenciais da API FedEx';

    public function handle()
    {
        $this->info('🔍 Diagnóstico detalhado da API FedEx');
        $this->info('=====================================');
        
        // Verificar configurações
        $this->info("\n📋 Configurações atuais:");
        $this->line('   Ambiente: ' . (config('services.fedex.use_production') ? 'Produção' : 'Homologação'));
        $this->line('   API URL: ' . config('services.fedex.api_url'));
        $this->line('   Client ID (Cotação/Envio): ' . config('services.fedex.client_id'));
        $this->line('   Client Secret (Cotação/Envio): ' . substr(config('services.fedex.client_secret'), 0, 5) . '...' . substr(config('services.fedex.client_secret'), -5));
        $this->line('   Client ID (Rastreamento): ' . config('services.fedex.tracking_client_id'));
        $this->line('   Client Secret (Rastreamento): ' . substr(config('services.fedex.tracking_client_secret'), 0, 5) . '...' . substr(config('services.fedex.tracking_client_secret'), -5));
        $this->line('   Shipper Account: ' . config('services.fedex.shipper_account'));
        
        // Teste 1: Autenticação básica para cotação/envio
        $this->info("\n🔐 Teste 1: Autenticação para cotação/envio");
        try {
            $response = Http::asForm()->post(config('services.fedex.api_url') . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.fedex.client_id'),
                'client_secret' => config('services.fedex.client_secret'),
            ]);
            
            $this->line('   Status Code: ' . $response->status());
            $this->line('   Response: ' . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                $this->info('   ✅ Autenticação bem-sucedida');
                $this->line('   Token: ' . substr($data['access_token'] ?? '', 0, 10) . '...' . substr($data['access_token'] ?? '', -10));
                $this->line('   Expira em: ' . ($data['expires_in'] ?? 'N/A') . ' segundos');
            } else {
                $this->error('   ❌ Falha na autenticação');
                $this->line('   Erro: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Exceção: ' . $e->getMessage());
        }
        
        // Teste 2: Autenticação básica para rastreamento
        $this->info("\n🔐 Teste 2: Autenticação para rastreamento");
        try {
            $response = Http::asForm()->post(config('services.fedex.api_url') . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.fedex.tracking_client_id'),
                'client_secret' => config('services.fedex.tracking_client_secret'),
            ]);
            
            $this->line('   Status Code: ' . $response->status());
            $this->line('   Response: ' . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                $this->info('   ✅ Autenticação bem-sucedida');
                $this->line('   Token: ' . substr($data['access_token'] ?? '', 0, 10) . '...' . substr($data['access_token'] ?? '', -10));
                $this->line('   Expira em: ' . ($data['expires_in'] ?? 'N/A') . ' segundos');
            } else {
                $this->error('   ❌ Falha na autenticação');
                $this->line('   Erro: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Exceção: ' . $e->getMessage());
        }
        
        // Teste 3: Verificar se as credenciais são diferentes
        $this->info("\n🔍 Teste 3: Verificação de credenciais");
        $clientIdShipping = config('services.fedex.client_id');
        $clientIdTracking = config('services.fedex.tracking_client_id');
        
        if ($clientIdShipping === $clientIdTracking) {
            $this->warn('   ⚠️ As credenciais de cotação/envio e rastreamento são iguais');
        } else {
            $this->info('   ✅ As credenciais de cotação/envio e rastreamento são diferentes');
        }
        
        // Teste 4: Verificar endpoints
        $this->info("\n🌐 Teste 4: Verificação de endpoints");
        $this->line('   Rate Endpoint: ' . config('services.fedex.rate_endpoint'));
        $this->line('   Ship Endpoint: ' . config('services.fedex.ship_endpoint'));
        $this->line('   Track Endpoint: ' . config('services.fedex.track_endpoint'));
        
        // Teste 5: Verificar se estamos em produção
        if (config('services.fedex.use_production')) {
            $this->info("\n⚠️ AVISO: Ambiente de produção detectado");
            $this->line('   As credenciais de produção podem ter restrições de IP ou ambiente.');
            $this->line('   Verifique se:');
            $this->line('   1. O IP do servidor está liberado na FedEx');
            $this->line('   2. As credenciais estão ativas');
            $this->line('   3. O ambiente tem permissão para usar credenciais de produção');
        }
        
        $this->info("\n🎯 Recomendações:");
        $this->line('   1. Se as autenticações falharem, verifique as credenciais');
        $this->line('   2. Se receber erro FORBIDDEN, pode ser restrição de IP');
        $this->line('   3. Para testes, use o ambiente de homologação (FEDEX_USE_PRODUCTION=false)');
        $this->line('   4. Para produção, confirme com a FedEx as permissões');
        
        return 0;
    }
} 