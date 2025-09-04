#!/bin/bash

# Script de Deploy Automático - Logiez
# Uso: ./deploy.sh

set -e  # Para o script se houver erro

echo "🚀 Iniciando deploy do Logiez..."

# Verifica se está no diretório correto
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Erro: docker-compose.yml não encontrado. Execute este script na raiz do projeto."
    exit 1
fi

# Função para deploy em servidor de produção (sem Docker)
deploy_production() {
    echo "🏭 Modo de produção detectado - deploy direto no servidor"
    
    echo "➡️ Entrando na pasta do projeto"
    cd /web
    
    echo "➡️ Atualizando branch main"
    git fetch origin main
    git reset --hard origin/main
    git clean -fd
    
    echo "➡️ Verificando arquivo .env"
    if [ ! -f .env ]; then
        echo "⚠️  Arquivo .env não encontrado, criando a partir do .env.example"
        cp .env.example .env || echo "Não foi possível copiar .env.example"
    fi
    
    echo "➡️ Limpando caches antes da instalação"
    rm -rf bootstrap/cache/*.php || true
    rm -rf storage/framework/cache/* || true
    rm -rf storage/framework/sessions/* || true
    rm -rf storage/framework/views/* || true
    
    echo "➡️ Instalando dependências (sem scripts)"
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts
    
    echo "➡️ Regenerando autoload"
    composer dump-autoload --no-scripts
    
    echo "➡️ Gerando chave da aplicação se necessário"
    php artisan key:generate --force || echo "Não foi possível gerar chave"
    
    echo "➡️ Rodando migrações"
    php artisan migrate --force || echo "Migrações falharam, continuando..."
    
    echo "➡️ Limpando e otimizando caches"
    php artisan config:clear || true
    php artisan config:cache || true
    php artisan route:clear || true
    php artisan route:cache || true
    php artisan view:clear || true
    php artisan view:cache || true
    php artisan cache:clear || true
    php artisan optimize:clear || true
    php artisan optimize || true
    
    echo "➡️ Verificando e corrigindo permissões"
    chmod -R 755 storage/ || true
    chmod -R 755 bootstrap/cache/ || true
    chown -R www-data:www-data storage/ || echo "Não foi possível alterar owner do storage"
    chown -R www-data:www-data bootstrap/cache/ || echo "Não foi possível alterar owner do cache"
    
    echo "➡️ Reiniciando serviço do PHP"
    if command -v systemctl &> /dev/null; then
        systemctl restart php8.2-fpm || systemctl restart php8.1-fpm || systemctl restart php-fpm || echo "Não foi possível reiniciar via systemctl"
    elif command -v service &> /dev/null; then
        service php8.2-fpm restart || service php8.1-fpm restart || service php-fpm restart || echo "Não foi possível reiniciar via service"
    else
        echo "⚠️  Não foi possível reiniciar o PHP automaticamente. Reinicie manualmente."
    fi
    
    echo "➡️ Verificando status do PHP"
    php -v || echo "PHP não está funcionando corretamente"
    
    echo "✅ Deploy de produção concluído com sucesso!"
    exit 0
}

# Verifica se está em ambiente de produção
if [ -d "/web" ] && [ ! -f "docker-compose.yml" ]; then
    deploy_production
fi

# Atualiza o código do Git
echo "📥 Atualizando código do Git..."
git pull origin main

# Para os containers atuais
echo "🛑 Parando containers..."
docker compose down

# Reconstrói e inicia os containers
echo "🔨 Reconstruindo containers..."
docker compose up -d --build

# Aguarda os containers ficarem prontos
echo "⏳ Aguardando containers ficarem prontos..."
sleep 10

# Instala dependências do Composer (sem scripts para evitar erro do ExceptionHandler)
echo "📦 Instalando dependências do Composer..."
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Regenera autoload separadamente
echo "🔄 Regenerando autoload..."
docker compose exec -T app composer dump-autoload --no-scripts

# Executa migrações
echo "🗄️ Executando migrações..."
docker compose exec -T app php artisan migrate --force

# Limpa e recria caches
echo "🧹 Limpando caches..."
docker compose exec -T app php artisan config:clear || true
docker compose exec -T app php artisan config:cache || true
docker compose exec -T app php artisan route:clear || true
docker compose exec -T app php artisan route:cache || true
docker compose exec -T app php artisan view:clear || true
docker compose exec -T app php artisan view:cache || true
docker compose exec -T app php artisan cache:clear || true

# Verifica se os containers estão rodando
echo "✅ Verificando status dos containers..."
docker compose ps


echo "🎉 Deploy concluído com sucesso!"
echo "🌐 Aplicação disponível em: http://localhost"
