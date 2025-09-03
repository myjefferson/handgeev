let hasUnsavedChanges = false;

// Função para atualizar o indicador de autosave
export function updateSaveIndicator(saving, saved) {
    const savingIcon = $('#savingIcon');
    const savedIcon = $('#savedIcon');
    const statusText = $('#saveStatusText');
    
    if (saving) {
        savingIcon.removeClass('hidden');
        savedIcon.addClass('hidden');
        statusText.text('Salvando...');
    } else if (saved) {
        savingIcon.addClass('hidden');
        savedIcon.removeClass('hidden');
        statusText.text('Todas as alterações salvas');
        
        // Esconder o ícone de salvo após 3 segundos
        setTimeout(() => {
            if (!hasUnsavedChanges) {
                savedIcon.addClass('hidden');
            }
        }, 3000);
    }
}

// Função para adicionar novo campo
export function addNewField(topicId) {
    const newRow = `
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200" data-topic-id="${topicId}">
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <input type="checkbox" checked="" class="visibility-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                </div>
            </td>
            <td class="px-6 py-4">
                <input type="text" class="key-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </td>
            <td class="px-6 py-4">
                <input type="text" class="value-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </td>
            <td class="px-6 py-4 flex space-x-2">
                <button type="button" class="save-row text-green-600 hover:text-green-800 dark:hover:text-green-400" title="Salvar">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
                <button type="button" class="remove-row text-red-600 hover:text-red-800 dark:hover:text-red-400" title="Remover">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        </tr>
    `;

    // Insere antes do trigger
    $(`.add-field-trigger[data-topic-id="${topicId}"]`).before(newRow);
}

// Função para feedback visual de salvamento
export function showSaveFeedback(row) {
    row.addClass('bg-green-50 dark:bg-green-900/20');
    setTimeout(() => {
        row.removeClass('bg-green-50 dark:bg-green-900/20');
    }, 1000);
}

// Adicionar novo campo
$('.add-field-trigger').on('click', function() {
    const topicId = $(this).data('topic-id');
    addNewField(topicId);
});

