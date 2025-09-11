# Estrutura da Tabela Users - Logiez

## Garantia de Estrutura

A tabela `users` é garantida para ter a estrutura exata especificada através da migration `2025_09_11_175022_ensure_users_table_structure.php`.

## Estrutura da Tabela

| Campo | Tipo | Null | Chave | Padrão | Extra |
|-------|------|------|-------|--------|-------|
| id | bigint(20) unsigned | NO | PRI | NULL | auto_increment |
| name | varchar(255) | NO | | NULL | |
| email | varchar(255) | NO | UNI | NULL | |
| email_verified_at | timestamp | YES | | NULL | |
| password | varchar(255) | NO | | NULL | |
| provider | varchar(255) | YES | | NULL | |
| provider_id | varchar(255) | YES | | NULL | |
| profile_type | enum('individual','business') | NO | | 'individual' | |
| document_number | varchar(20) | YES | | NULL | |
| phone | varchar(20) | YES | | NULL | |
| address | varchar(255) | YES | | NULL | |
| address_number | varchar(20) | YES | | NULL | |
| address_complement | varchar(100) | YES | | NULL | |
| city | varchar(100) | YES | | NULL | |
| state | varchar(50) | YES | | NULL | |
| postal_code | varchar(20) | YES | | NULL | |
| country | varchar(2) | NO | | 'BR' | |
| remember_token | varchar(100) | YES | | NULL | |
| api_token | varchar(100) | YES | UNI | NULL | |
| is_active | tinyint(1) | NO | | '1' | |
| admin | tinyint(1) | NO | | '0' | |
| last_login_at | timestamp | YES | | NULL | |
| created_at | timestamp | YES | | NULL | |
| updated_at | timestamp | YES | | NULL | |

## Como Funciona

### Durante o Deploy Automático

1. **Migration Principal**: A migration `ensure_users_table_structure` é executada automaticamente
2. **Recriação da Tabela**: A tabela é recriada com a estrutura exata especificada
3. **Usuário Admin**: Um usuário admin padrão é criado se não existir
4. **Verificação**: O script de deploy verifica se a migration foi executada

### Comandos de Verificação

```bash
# Verificar status das migrations
php artisan migrate:status

# Verificar estrutura da tabela
php artisan tinker
>>> Schema::getColumnListing('users')
>>> DB::select('DESCRIBE users')
```

### Deploy Manual

```bash
# Executar apenas a migration da estrutura
php artisan migrate --path=database/migrations/2025_09_11_175022_ensure_users_table_structure.php

# Ou executar todas as migrations
php artisan migrate
```

## Usuário Admin Padrão

- **Email**: pedro.eng98@gmail.com
- **Senha**: 123456
- **Perfil**: individual
- **Admin**: true
- **Ativo**: true

## Notas Importantes

- A migration recria a tabela completamente, então **todos os dados existentes serão perdidos**
- Para produção, faça backup antes de executar
- A migration é idempotente - pode ser executada múltiplas vezes sem problemas
- O usuário admin só é criado se não existir um usuário com o email especificado
