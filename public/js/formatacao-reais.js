// Formatação em Reais Brasileiros
function formatarReais(valor) {
    // Remover tudo que não é número
    const numero = valor.toString().replace(/\D/g, '');
    
    // Se não há número, retornar vazio
    if (numero === '') return '';
    
    // Converter para número e dividir por 100 para considerar centavos
    const valorNumerico = parseInt(numero) / 100;
    
    // Formatar como moeda brasileira
    return valorNumerico.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Função para converter valor formatado em reais para número
function converterReaisParaNumero(valorFormatado) {
    // Remover pontos e vírgulas, substituir vírgula por ponto
    const numero = valorFormatado.replace(/\./g, '').replace(',', '.');
    return parseFloat(numero) || 0;
}

// Inicializar formatação do campo de valor unitário
function inicializarFormatacaoValor() {
    const campoValor = $('#produto-valor');
    
    // Evento de foco - limpar o campo
    campoValor.on('focus', function() {
        $(this).val('');
    });
    
    // Evento de input - formatar em tempo real
    campoValor.on('input', function() {
        const valor = $(this).val();
        const valorFormatado = formatarReais(valor);
        $(this).val(valorFormatado);
    });
    
    // Evento de blur - garantir formatação final
    campoValor.on('blur', function() {
        const valor = $(this).val();
        if (valor === '') {
            $(this).val('0,00');
        } else {
            const valorFormatado = formatarReais(valor);
            $(this).val(valorFormatado);
        }
    });
}

// Inicializar quando o documento estiver pronto
$(document).ready(function() {
    inicializarFormatacaoValor();
}); 