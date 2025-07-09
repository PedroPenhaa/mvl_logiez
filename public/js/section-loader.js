// Função para carregar seções dinamicamente
function loadSection(sectionName) {
    // Mostrar loader
    $('#content-loader').show();
    
    // Remover estilos específicos da seção anterior
    $('link[data-section-style]').remove();
    
    // Fazer requisição AJAX para carregar a seção
    $.ajax({
        url: `/sections/${sectionName}`,
        type: 'GET',
        success: function(response) {
            // Esconder loader
            $('#content-loader').hide();
            
            // Atualizar conteúdo
            $('.main-content').html(response);
            
            // Carregar estilos específicos da seção
            if (sectionName === 'cotacao') {
                if (!$('link[href*="cotacao.css"]').length) {
                    const styleLink = document.createElement('link');
                    styleLink.rel = 'stylesheet';
                    styleLink.href = '/css/cotacao.css';
                    styleLink.setAttribute('data-section-style', 'true');
                    document.head.appendChild(styleLink);
                }
            }
            
            // Atualizar URL sem recarregar a página
            window.history.pushState({}, '', `/${sectionName}`);
            
            // Atualizar item ativo no menu
            $('.menu-item').removeClass('active');
            $(`.menu-item[data-section="${sectionName}"]`).addClass('active');
        },
        error: function() {
            // Esconder loader
            $('#content-loader').hide();
            
            // Mostrar mensagem de erro
            alert('Erro ao carregar a seção. Por favor, tente novamente.');
        }
    });
}

// Manipular cliques nos itens do menu
$(document).ready(function() {
    $('.menu-item').on('click', function(e) {
        e.preventDefault();
        const sectionName = $(this).data('section');
        if (sectionName) {
            loadSection(sectionName);
        }
    });
}); 