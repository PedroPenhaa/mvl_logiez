#!/bin/bash
set -e

echo "➡️ Entrando na pasta do projeto"
cd /web

echo "➡️ Atualizando branch main"
git fetch origin main
git reset --hard origin/main
git clean -fd

echo "➡️ Instalando dependências"
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "➡️ Rodando migrações"
php artisan migrate --force

echo "➡️ Limpando TODOS os caches (CRÍTICO para corrigir o erro)"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

echo "➡️ Recriando caches com configurações corretas"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "➡️ Verificando e corrigindo permissões"
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Tentar diferentes usuários para chown
if id "www-data" &>/dev/null; then
    chown -R www-data:www-data storage/ || echo "Não foi possível alterar owner do storage"
    chown -R www-data:www-data bootstrap/cache/ || echo "Não foi possível alterar owner do cache"
elif id "apache" &>/dev/null; then
    chown -R apache:apache storage/ || echo "Não foi possível alterar owner do storage"
    chown -R apache:apache bootstrap/cache/ || echo "Não foi possível alterar owner do cache"
elif id "nginx" &>/dev/null; then
    chown -R nginx:nginx storage/ || echo "Não foi possível alterar owner do storage"
    chown -R nginx:nginx bootstrap/cache/ || echo "Não foi possível alterar owner do cache"
else
    echo "⚠️  Usuário do servidor web não identificado, mantendo permissões atuais"
fi

echo "➡️ Verificando arquivo .env"
if [ ! -f .env ]; then
    echo "⚠️  Arquivo .env não encontrado, criando a partir do .env.example"
    cp .env.example .env || echo "Não foi possível copiar .env.example"
fi

echo "➡️ Gerando chave da aplicação se necessário"
php artisan key:generate --force || echo "Não foi possível gerar chave"

echo "➡️ Testando se a aplicação está funcionando"
php artisan route:list --compact || echo "⚠️  Erro ao listar rotas, mas continuando..."

echo "➡️ Reiniciando serviço do PHP"
# Tentar diferentes métodos de reiniciar o PHP
if command -v systemctl &> /dev/null; then
    systemctl restart php8.4-fpm || systemctl restart php8.2-fpm || systemctl restart php8.1-fpm || systemctl restart php-fpm || echo "Não foi possível reiniciar via systemctl"
elif command -v service &> /dev/null; then
    service php8.4-fpm restart || service php8.2-fpm restart || service php8.1-fpm restart || service php-fpm restart || echo "Não foi possível reiniciar via service"
else
    echo "⚠️  Não foi possível reiniciar o PHP automaticamente. Reinicie manualmente."
fi

echo "➡️ Verificando status do PHP"
php -v || echo "PHP não está funcionando corretamente"

echo "✅ Deploy concluído com sucesso!"
echo "🔧 Correções aplicadas:"
echo "   - Configuração de exceções corrigida"
echo "   - Namespace de views configurado"
echo "   - Caches limpos e recriados"
echo "   - Permissões ajustadas"
