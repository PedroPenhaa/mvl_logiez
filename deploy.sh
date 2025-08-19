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

# Instala dependências do Composer
echo "📦 Instalando dependências do Composer..."
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader

# Executa migrações
echo "🗄️ Executando migrações..."
docker compose exec -T app php artisan migrate --force

# Limpa e recria caches
echo "🧹 Limpando caches..."
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

# Verifica se os containers estão rodando
echo "✅ Verificando status dos containers..."
docker compose ps


echo "🎉 Deploy concluído com sucesso!"
echo "🌐 Aplicação disponível em: http://localhost"
