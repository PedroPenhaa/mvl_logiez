# Usa a imagem oficial do PHP com suporte a FPM
FROM php:8.2-fpm

# Instala extensões necessárias para Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    curl \
    git \
    nginx \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql gd

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do Laravel para o container
COPY . .

# Ajusta permissões das pastas necessárias
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copia arquivo de configuração do nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Criar script de inicialização
RUN echo '#!/bin/bash\n\
echo "Aguardando o banco de dados..."\n\
while ! mysql -h 127.0.0.1 -u root -padmin123 -e "SELECT 1;" >/dev/null 2>&1; do\n\
  echo "Banco de dados ainda não está pronto..."\n\
  sleep 2\n\
done\n\
echo "Banco de dados está pronto!"\n\
composer install --no-interaction --prefer-dist --optimize-autoloader\n\
php artisan cache:clear\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
php artisan migrate --force\n\
\n\
# Iniciar PHP-FPM em background\n\
php-fpm -D\n\
\n\
# Iniciar Nginx em foreground\n\
nginx -g "daemon off;"\n'\
> /start.sh && chmod +x /start.sh

# Exposição de portas
EXPOSE 80 9000

# Comando para iniciar o container com o script de inicialização
CMD ["/start.sh"]
