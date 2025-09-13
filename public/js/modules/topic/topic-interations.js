$(document).on('click', '.topic-item button', function(e) {
    // Prevenir que o clique no botão de delete propague
    if ($(e.target).closest('.delete-topic-btn').length) {
        return;
    }
    
    const topicId = $(this).closest('.topic-item').data('topic-id');
    // const topicTitle = $(this).closest('.topic-item').find('.topic-title').text();
    // $('.title-header').text(topicTitle)
    
    // Atualizar a sidebar
    $('.topic-item button').removeClass('bg-teal-400/20 text-teal-400 border-teal-400/30').addClass('text-gray-400 hover:text-teal-300 hover:bg-slate-750');
    $(this).removeClass('text-gray-400 hover:text-teal-300 hover:bg-slate-750').addClass('bg-teal-400/20 text-teal-400 border border-teal-400/30');
    
    // Mostrar apenas o conteúdo do tópico selecionado
    $('.topic-content').addClass('hidden');
    $(`.topic-content[data-topic-id="${topicId}"]`).removeClass('hidden');
});