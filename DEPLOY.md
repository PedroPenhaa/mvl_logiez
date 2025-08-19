# 🚀 Deploy Automático - Logiez

Este projeto está configurado para deploy automático via GitHub Actions.

## 📋 Pré-requisitos

1. **Servidor configurado** com:
   - Docker e Docker Compose
   - Git
   - Acesso SSH

2. **Repositório GitHub** com:
   - Secrets configurados (veja abaixo)

## ⚙️ Configuração dos Secrets no GitHub

### 1. Acesse seu repositório no GitHub
- Vá para: `https://github.com/SEU_USUARIO/mvl_logiez`

### 2. Configure os Secrets
- Clique em **Settings** → **Secrets and variables** → **Actions**
- Clique em **New repository secret**
- Adicione os seguintes secrets:

| Secret Name | Valor | Descrição |
|-------------|-------|-----------|
| `SERVER_HOST` | `us1.magen.in` | IP ou domínio do servidor |
| `SERVER_USER` | `logiezlogi` | Usuário SSH do servidor |
| `SERVER_PASS` | `!ynJdpC89Z` | Senha SSH do servidor |

## 🔄 Como Funciona

1. **Push na branch main** → Dispara o workflow
2. **GitHub Actions** → Conecta no servidor via SSH
3. **Executa comandos**:
   - `cd /home/logiezlogi/app`
   - `git pull origin main`
   - `docker compose down`
   - `docker compose up -d --build`
   - Instala dependências do Composer
   - Executa migrações do banco
   - Limpa caches do Laravel

## 📁 Estrutura de Arquivos

```
.github/
└── workflows/
    └── deploy.yml          # Workflow do GitHub Actions

deploy.sh                   # Script manual de deploy
DEPLOY.md                   # Esta documentação
```

## 🛠️ Deploy Manual (Alternativo)

Se precisar fazer deploy manualmente no servidor:

```bash
# No servidor
cd /home/logiezlogi/app
./deploy.sh
```

## 🔍 Monitoramento

- **GitHub Actions**: Acompanhe os deploys em `Actions` no GitHub
- **Logs do servidor**: Verifique os logs dos containers Docker
- **Status da aplicação**: Acesse a URL do seu domínio

## 🚨 Troubleshooting

### Erro de conexão SSH
- Verifique se os secrets estão corretos
- Teste a conexão SSH manualmente
- Confirme se o usuário tem permissões

### Erro no Docker
- Verifique se o Docker está rodando no servidor
- Confirme se o caminho `/home/logiezlogi/app` existe
- Verifique os logs: `docker compose logs`

### Erro nas migrações
- Verifique se o banco de dados está acessível
- Confirme as variáveis de ambiente no `.env`

## 📞 Suporte

Para problemas com o deploy, verifique:
1. Logs do GitHub Actions
2. Logs dos containers Docker
3. Status do servidor SSH
