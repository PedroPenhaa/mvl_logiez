# Diagnóstico FedEx - Erro Account Number Mismatch

## Resumo do Problema

Estamos enfrentando o erro `ACCOUNT.NUMBER.MISMATCH` ao tentar fazer cotações na API de produção da FedEx. O erro específico é:

```
"Account Number Mismatch - As the payment Type is SENDER, ShippingChargesPayment Payor AccountNumber should match the shipper account number. Please update and try again"
```

## Configurações Atuais

### Credenciais de Produção (Cotação e Envio)
- **Client ID**: l7f78ac0672e85493bb08276396843e381
- **Client Secret**: 46fbaca66ec94666b2724c09a99b3844
- **API URL**: https://apis.fedex.com
- **Shipper Account**: 204585494 (atualizada)

### Credenciais de Produção (Rastreamento)
- **Client ID**: l746168d7b2428456a8a5874a15b34a65b
- **Client Secret**: e279ac21793e4ae89686b71507746fab

## Testes Realizados

### 1. Autenticação ✅
- Token de autenticação obtido com sucesso
- Credenciais estão válidas

### 2. Estrutura da Requisição ✅
- Seguindo exatamente a documentação da Rates and Transit Times API
- Incluindo `accountNumber` no nível raiz
- Endereços completos com cidade, estado e código postal
- Testado com e sem `shippingChargesPayment`

### 3. Ambiente de Homologação ✅
- Funciona corretamente (erro diferente: `COUNTRY.POSTALCODE.INVALID`)
- Confirma que a estrutura da requisição está correta

### 4. Ambiente de Produção ❌
- Erro `ACCOUNT.NUMBER.MISMATCH` persistente
- Testado com conta `510087020` (original) e `204585494` (nova)
- Mesmo erro com diferentes estruturas de requisição

## Requisição de Teste (Atualizada)

```json
{
  "accountNumber": {
    "value": "204585494"
  },
  "rateRequestControlParameters": {
    "returnTransitTimes": true,
    "servicesNeededOnRateFailure": true,
    "variableOptions": "FREIGHT_GUARANTEE",
    "rateSortOrder": "SERVICENAMETRADITIONAL"
  },
  "requestedShipment": {
    "shipper": {
      "address": {
        "streetLines": ["Rua Teste, 123"],
        "city": "São Paulo",
        "stateOrProvinceCode": "SP",
        "postalCode": "13152-164",
        "countryCode": "BR",
        "residential": false
      }
    },
    "recipient": {
      "address": {
        "streetLines": ["Test Street, 456"],
        "city": "Orlando",
        "stateOrProvinceCode": "FL",
        "postalCode": "34747",
        "countryCode": "US",
        "residential": false
      }
    },
    "preferredCurrency": "USD",
    "rateRequestType": ["LIST", "ACCOUNT"],
    "shipDateStamp": "2025-07-18",
    "pickupType": "DROPOFF_AT_FEDEX_LOCATION",
    "packagingType": "YOUR_PACKAGING",
    "shippingChargesPayment": {
      "paymentType": "SENDER",
      "payor": {
        "responsibleParty": {
          "accountNumber": {
            "value": "204585494"
          }
        }
      }
    },
    "requestedPackageLineItems": [
      {
        "weight": {
          "units": "KG",
          "value": 5
        },
        "dimensions": {
          "length": 20,
          "width": 10,
          "height": 20,
          "units": "CM"
        },
        "groupPackageCount": 1
      }
    ],
    "totalPackageCount": 1
  },
  "carrierCodes": ["FDXE", "FDXG"]
}
```

## Análise do Problema

### Testes com Diferentes Contas:
1. **Conta 510087020**: Erro `ACCOUNT.NUMBER.MISMATCH`
2. **Conta 204585494**: Mesmo erro `ACCOUNT.NUMBER.MISMATCH`

### Conclusão:
O problema **NÃO** é específico da conta, mas sim das **permissões da API Key**. A API Key `l7f78ac0672e85493bb08276396843e381` não tem permissão para fazer cotações em produção.

## Possíveis Causas

1. **API Key sem permissão**: A API Key pode não ter permissão para fazer cotações em produção
2. **API Key restrita**: A API Key pode estar restrita apenas para rastreamento
3. **Configuração incorreta**: A API Key pode estar configurada apenas para sandbox
4. **Restrições de IP**: Pode haver restrições de IP para essa API Key
5. **Tipo de conta**: A API Key pode estar associada a um tipo de conta que não permite cotações

## Ações Necessárias

### Para a FedEx:
1. **Verificar permissões da API Key**: Confirmar se a API Key `l7f78ac0672e85493bb08276396843e381` tem permissão para fazer cotações em produção
2. **Verificar tipo de API Key**: Confirmar se a API Key está configurada para Rates and Transit Times API
3. **Verificar restrições**: Confirmar se há restrições de IP ou ambiente para essa API Key
4. **Fornecer nova API Key**: Se necessário, fornecer uma nova API Key com permissões corretas
5. **Validar configuração**: Confirmar se a API Key está ativa e configurada corretamente

### Para o Desenvolvedor:
1. **Contatar suporte FedEx**: Abrir um ticket com o suporte da FedEx
2. **Fornecer logs completos**: Enviar logs detalhados das requisições
3. **Solicitar validação**: Pedir para a FedEx validar as permissões da API Key
4. **Solicitar nova API Key**: Se necessário, solicitar uma nova API Key com permissões corretas

## Status Atual

- ✅ **Autenticação**: Funcionando
- ✅ **Estrutura da API**: Correta
- ✅ **Ambiente de Homologação**: Funcionando
- ❌ **Ambiente de Produção**: Bloqueado por erro de permissões da API Key

## Próximos Passos

1. Abrir ticket com o suporte da FedEx
2. Fornecer este documento como referência
3. Solicitar validação das permissões da API Key
4. Solicitar nova API Key se necessário
5. Aguardar resposta da FedEx para prosseguir

---

**Data**: 18/07/2025  
**Ambiente**: Produção  
**API**: Rates and Transit Times API  
**Erro**: ACCOUNT.NUMBER.MISMATCH  
**Causa**: Permissões da API Key (não da conta) 