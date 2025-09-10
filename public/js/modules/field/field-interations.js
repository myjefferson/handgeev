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

// Função para verificar se pode adicionar mais campos
export function canAddMoreFields() {
    return typeof window.canAddMoreFields !== 'undefined' ? window.canAddMoreFields : true;
}

// Função para mostrar alerta de limite atingido
export function showLimitAlert() {
    alert('Você atingiu o limite de campos do seu plano. Faça upgrade para adicionar mais campos.');
}

// Função para atualizar a UI dos botões de adicionar campo
export function updateAddFieldButtons() {
    const topicContents = document.querySelectorAll('.topic-content');
    
    topicContents.forEach(topicContent => {
        const topicId = topicContent.dataset.topicId;
        const addTrigger = topicContent.querySelector('.add-field-trigger');
        const limitRow = topicContent.querySelector('.limit-reached-row');
        
        if (canAddMoreFields()) {
            // Mostrar botão de adicionar e esconder mensagem de limite
            if (addTrigger) addTrigger.style.display = 'table-row';
            if (limitRow) limitRow.style.display = 'none';
        } else {
            // Esconder botão de adicionar e mostrar mensagem de limite
            if (addTrigger) addTrigger.style.display = 'none';
            if (limitRow) limitRow.style.display = 'table-row';
        }
    });
}

// Função para atualizar contador de campos
export function updateFieldsCounter(change = 1) {
    if (typeof window.currentFieldsCount !== 'undefined' && 
        typeof window.fieldsLimit !== 'undefined') {
        
        window.currentFieldsCount += change;
        window.canAddMoreFields = window.currentFieldsCount < window.fieldsLimit;
        
        // Atualizar a UI dos botões
        updateAddFieldButtons();
        
        // Atualizar mensagens de limite se existirem
        updateLimitMessages();
    }
}

// Função para atualizar mensagens de limite
function updateLimitMessages() {
    const limitMessages = document.querySelectorAll('.fields-limit-message');
    
    limitMessages.forEach(message => {
        if (!window.canAddMoreFields && window.fieldsLimit > 0) {
            message.innerHTML = `
                ⚠️ Limite de campos atingido (${window.currentFieldsCount}/${window.fieldsLimit}). 
                <a href="{{ route('landing.plans') }}" class="underline font-medium">Faça upgrade</a> 
                para adicionar mais campos.
            `;
            message.parentElement.style.display = 'block';
        } else if (window.fieldsLimit > 0) {
            message.innerHTML = `
                📊 Campos utilizados: ${window.currentFieldsCount}/${window.fieldsLimit} 
                (${window.fieldsLimit - window.currentFieldsCount} restantes)
            `;
            message.parentElement.style.display = 'block';
        } else {
            // Se for ilimitado, esconder a mensagem
            message.parentElement.style.display = 'none';
        }
    });
}

// Função para adicionar novo campo
export function addNewField(topicId) {
    if (!canAddMoreFields()) {
        showLimitAlert();
        return null;
    }
    
    const newRow = `
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 new-field" data-id="" data-topic-id="${topicId}">
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <input type="checkbox" checked class="visibility-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                </div>
            </td>
            <td class="px-6 py-4">
                <input type="text" class="key-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Nome da chave">
            </td>
            <td class="px-6 py-4">
                <input type="text" class="value-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500" placeholder="Valor">
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

    const trigger = $(`.add-field-trigger[data-topic-id="${topicId}"]`);
    trigger.before(newRow);
    
    // Atualiza o contador (assume que será salvo)
    updateFieldsCounter(1);
    
    return trigger.prev();
}

// Função para remover campo (atualiza contador)
export function removeFieldCounter() {
    if (typeof window.currentFieldsCount !== 'undefined' && 
        typeof window.fieldsLimit !== 'undefined') {
        
        window.currentFieldsCount = Math.max(0, window.currentFieldsCount - 1);
        window.canAddMoreFields = window.currentFieldsCount < window.fieldsLimit;
        
        // Atualizar a UI dos botões
        updateAddFieldButtons();
        
        // Atualizar mensagens de limite se existirem
        updateLimitMessages();
    }
}

// Função para feedback visual de salvamento
export function showSaveFeedback(row) {
    row.addClass('bg-green-50 dark:bg-green-900/20');
    setTimeout(() => {
        row.removeClass('bg-green-50 dark:bg-green-900/20');
    }, 1000);
}

// Adicionar novo campo com verificação de limite
$(document).on('click', '.add-field-trigger', function() {
    const topicId = $(this).data('topic-id');
    addNewField(topicId);
});

// Inicializar quando o documento estiver pronto
$(document).ready(function() {
    // Inicializar a UI dos botões e mensagens
    if (typeof window.canAddMoreFields !== 'undefined' && 
        typeof window.fieldsLimit !== 'undefined' && 
        typeof window.currentFieldsCount !== 'undefined') {
        
        updateAddFieldButtons();
        updateLimitMessages();
    }
});

export function handleFieldCreationError(xhr) {
    if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.error === 'limit_exceeded') {
        // Atualizar as variáveis globais para refletir o limite
        if (typeof window.fieldsLimit !== 'undefined') {
            window.currentFieldsCount = window.fieldsLimit;
            window.canAddMoreFields = false;
            refreshFieldsUI();
        }
        return true; // Indicar que foi tratado
    }
    return false;
}

// Função para forçar atualização da UI (pode ser chamada de outros arquivos)
export function refreshFieldsUI() {
    updateAddFieldButtons();
    updateLimitMessages();
}