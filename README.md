# Logiez - Sistema de Logística e Entregas

## Sobre o Projeto

Logiez é um sistema de logística e entregas que permite o envio de pacotes, gerenciamento de pagamentos e rastreamento de encomendas. O sistema integra-se com a FedEx para envio e rastreamento, e com o gateway de pagamentos Asaas para processamento de pagamentos.

## Funcionalidades Implementadas

### Envio de Pacotes
- Cálculo de cotações de frete baseado em peso e dimensões
- Conversão automática de USD para BRL nas cotações da FedEx
- Seleção de serviços de entrega com diferentes prazos e valores
- Integração com API FedEx para processamento de envios

### Pagamentos
- Integração com o gateway Asaas
- Múltiplos métodos de pagamento (boleto, pix, cartão de crédito)
- Persistência do status de pagamento
- Atualização automática do status de pagamento através de comando `app:atualizar-status-pagamentos`

### Rastreamento
- Interface amigável para rastreamento de pacotes
- Visualização do histórico de eventos de rastreamento
- Simulação de rastreamento para ambiente de desenvolvimento

### Depuração
- Logs detalhados para frontend e backend
- Controle de exibição de logs baseado no perfil do usuário e ambiente
- Registro de todas as comunicações com APIs externas

## Estrutura de Dados

### Shipment
Tabela responsável pelo armazenamento dos envios, com os seguintes campos principais:
- `user_id`: ID do usuário que criou o envio
- `tracking_number`: Número de rastreamento
- `carrier`: Transportadora utilizada
- `service_code`: Código do serviço de entrega
- `status`: Status atual do envio
- `total_price`: Valor total do envio em BRL
- `package_weight`: Peso do pacote
- `package_length`, `package_width`, `package_height`: Dimensões

### Payment
Tabela responsável pelo armazenamento dos pagamentos, com os campos:
- `user_id`: ID do usuário
- `shipment_id`: ID do envio
- `transaction_id`: ID da transação no gateway
- `payment_method`: Método de pagamento (boleto, pix, credit_card)
- `amount`: Valor do pagamento
- `currency`: Moeda (BRL)
- `status`: Status do pagamento (pending, processing, paid, failed, canceled)
- `payment_date`: Data de pagamento
- `due_date`: Data de vencimento para boletos
- `payer_name`: Nome do pagador
- `payer_document`: Documento do pagador (CPF/CNPJ)
- `invoice_url`: URL para visualização/impressão do boleto
- `barcode`: Código de barras do boleto
- `payment_link`: Link de pagamento
- `gateway_response`: Resposta completa do gateway

## Configuração do Ambiente

### Requisitos
- PHP 8.0+
- Composer
- MySQL/MariaDB 5.7+
- Node.js 14+ e NPM

### Variáveis de Ambiente
Configure seu arquivo `.env` com as seguintes variáveis:

```
# Configuração da FedEx
FEDEX_API_KEY=sua_chave_api
FEDEX_SECRET=seu_segredo
FEDEX_ACCOUNT_NUMBER=seu_numero_de_conta
FEDEX_METER_NUMBER=seu_numero_de_medidor
FEDEX_TEST_MODE=true

# Configuração do Asaas
ASAAS_API_TOKEN=seu_token_api
ASAAS_SANDBOX=true

# Configuração de conversão de moeda
CURRENCY_API_KEY=sua_chave_api_conversao
```

### Comandos de Atualização
Foi implementado um comando para atualização automática do status dos pagamentos:

```bash
php artisan app:atualizar-status-pagamentos
```

Este comando deve ser programado para execução periódica através do Cron:

```
* * * * * cd /caminho/para/seu/projeto && php artisan schedule:run >> /dev/null 2>&1
```

## Depuração e Logs

O sistema implementa logs detalhados em vários níveis:

1. Logs de Sistema: Disponíveis em `storage/logs/laravel.log`
2. Logs de Frontend: Disponíveis no console do navegador e na seção de depuração da interface
3. Logs de API: Todas as chamadas e respostas de APIs estão disponíveis nos logs

Para acessar os logs na interface, é necessário ser administrador ou estar em ambiente de desenvolvimento.

## Integrações

### FedEx
O sistema se integra com a FedEx para:
- Obter cotações de envio
- Processar envios
- Rastrear pacotes

### Asaas
A integração com o Asaas permite:
- Processamento de pagamentos via boleto
- Processamento de pagamentos via PIX
- Processamento de pagamentos via cartão de crédito
- Verificação do status de pagamentos

## Contribuição

Para contribuir com o projeto:
1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/minha-feature`)
3. Commit suas mudanças (`git commit -m 'Adicionar minha feature'`)
4. Push para a branch (`git push origin feature/minha-feature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE para mais detalhes.
