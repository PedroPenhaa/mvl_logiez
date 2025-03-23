#!/bin/bash

# Verifica se o container está rodando
if [ "$(docker ps -q -f name=laravel_app)" ]; then
    echo "Executando comando no container laravel_app..."
    docker exec -it laravel_app php "$@"
else
    echo "O container laravel_app não está rodando!"
    exit 1
fi
