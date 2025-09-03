
// Navegação entre tabs de tópicos
$('.topic-tab').on('click', function() {
    const topicId = $(this).data('topic-id');
    
    // Atualizar tabs ativas
    $('.topic-tab').removeClass('border-blue-600 text-blue-600').addClass('border-transparent');
    $(this).addClass('border-blue-600 text-blue-600').removeClass('border-transparent');
    
    // Mostrar conteúdo do tópico selecionado
    $('.topic-content').addClass('hidden');
    $(`.topic-content[data-topic-id="${topicId}"]`).removeClass('hidden');
});