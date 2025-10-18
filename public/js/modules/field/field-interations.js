// public/js/modules/field/field-interations.js
let hasUnsavedChanges = false;

// Função para verificar se o plano é ilimitado
export function isUnlimitedPlan() {
    return typeof window.fieldsLimit !== 'undefined' && window.fieldsLimit === 0;
}

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
export function canAddMoreFields(topicId = null) {
    if (!topicId) {
        return typeof window.globalCanAddMoreFields !== 'undefined' ? window.globalCanAddMoreFields : true;
    }
    
    // Verificar limites específicos do tópico
    const topicLimits = window.getTopicLimits(topicId);
    
    if (!topicLimits) {
        return true; // Fallback seguro
    }
    
    const canAdd = topicLimits.isUnlimited || topicLimits.canAddMoreFields;    
    return canAdd;
}

// Função para mostrar alerta de limite atingido
export function showLimitAlert() {
    alert('Você atingiu o limite de campos do seu plano. Faça upgrade para adicionar mais campos.');
}

// Função para atualizar a UI dos botões de adicionar campo (ATUALIZADA)
export function updateAddFieldButtons() {    
    $('.topic-content').each(function() {
        const topicId = $(this).data('topic-id');
        const addTrigger = $(this).find('.add-field-trigger');
        const limitRow = $(this).find('.limit-reached-row');
        
        if (!topicId) {
            return;
        }
        
        const topicLimits = window.getTopicLimits(topicId);
        
        if (!topicLimits) {
            return;
        }
        
        const canAdd = topicLimits.isUnlimited || topicLimits.canAddMoreFields;        
        if (canAdd) {
            // Pode adicionar - mostrar botão, esconder mensagem de limite
            if (addTrigger.length) {
                addTrigger.show();
                const addFieldText = window.translations?.workspace?.table?.add_field?.trigger || 'Adicionar campo';
                addTrigger.find('td').html(`
                    <div class="flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        ${addFieldText}
                    </div>
                `);
            }
            if (limitRow.length) {
                limitRow.hide();
            }
        } else {
            // Não pode adicionar - esconder botão, mostrar mensagem de limite
            if (addTrigger.length) {
                addTrigger.hide();
            }
            if (limitRow.length) {
                limitRow.show();
                limitRow.find('td').html(`
                    <div class="flex items-center justify-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Limite de ${topicLimits.fieldsLimit} campos por tópico atingido.
                        <a href="/subscription/pricing" class="underline ml-1 text-white">
                            Faça upgrade
                        </a>
                    </div>
                `);
            }
        }
    });
}

//Atualizar contadores no sidebar
export function updateSidebarCounters() {
    $('.topic-item').each(function() {
        const topicId = $(this).data('topic-id');
        const counter = $(this).find('.text-xs');
        
        if (topicId && counter.length) {
            const topicLimits = window.getTopicLimits(topicId);
            if (topicLimits) {
                counter.text(`${topicLimits.currentFieldsCount}`);
            }
        }
    });
}

// Função para atualizar contador de campos
export function updateFieldsCounter(change = 1) {
    if (typeof window.currentFieldsCount !== 'undefined' && 
        typeof window.fieldsLimit !== 'undefined') {
        
        window.currentFieldsCount += change;
        
        // Para planos ilimitados, sempre pode adicionar mais campos
        if (isUnlimitedPlan()) {
            window.canAddMoreFields = true;
        } else {
            // Para planos limitados, verificar se ainda pode adicionar
            window.canAddMoreFields = window.currentFieldsCount < window.fieldsLimit;
        }
        
        // Atualizar a UI dos botões
        updateAddFieldButtons();
        updateLimitMessages();
    }
}

// Função para atualizar mensagens de limite (ATUALIZADA)
function updateLimitMessages() {
    const limitMessages = document.querySelectorAll('.fields-limit-message');
    
    limitMessages.forEach(message => {
        // Esconder mensagem se for plano ilimitado ou admin
        if (isUnlimitedPlan()) {
            message.parentElement.style.display = 'none';
            return;
        }
        
        if (!window.canAddMoreFields && window.fieldsLimit > 0) {
            message.innerHTML = `
                Limite de campos atingido (${window.currentFieldsCount}/${window.fieldsLimit}). 
                <a href="{{ route('subscription.pricing') }}" class="underline font-medium">Faça upgrade</a> 
                para adicionar mais campos.
            `;
            message.parentElement.style.display = 'block';
        } else if (window.fieldsLimit > 0) {
            // Usar remainingFields se disponível, senão calcular
            const remaining = typeof window.remainingFields !== 'undefined' 
                ? window.remainingFields 
                : (window.fieldsLimit - window.currentFieldsCount);
                
            message.innerHTML = `
                Campos utilizados: ${window.currentFieldsCount}/${window.fieldsLimit} 
                (${remaining} restantes)
            `;
            message.parentElement.style.display = 'block';
        } else {
            message.parentElement.style.display = 'none';
        }
    });
}

// Função para criar input baseado no tipo (NOVA FUNÇÃO)
export function createValueInputByType(type, currentValue = '') {
    switch(type) {
        case 'boolean':
            return `
                <select name="key_value" class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                    <option value="true" ${currentValue === 'true' ? 'selected' : ''}>Verdadeiro</option>
                    <option value="false" ${currentValue === 'false' ? 'selected' : ''}>Falso</option>
                </select>
            `;
        case 'number':
            return `
                <input type="number" name="key_value" value="${currentValue}" 
                    class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" 
                    placeholder="Digite um número" step="any">
            `;
        case 'text':
        default:
            return `
                <input type="text" name="key_value" value="${currentValue}" 
                    class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" 
                    placeholder="Digite o valor">
            `;
    }
}

// Função para atualizar input quando tipo muda (NOVA FUNÇÃO)
export function updateFieldInputType(row, type) {
    const valueCell = row.find('td').eq(2);
    const currentValue = valueCell.find('.value-input').val() || '';
    
    // Remove o input atual
    valueCell.find('.value-input').remove();
    
    // Cria o novo input baseado no tipo
    const newInput = createValueInputByType(type, currentValue);
    valueCell.append(newInput);
}

// Função para adicionar novo campo (ATUALIZADA COM TIPAGEM)
export function addNewField(topicId) {
    if (!canAddMoreFields(topicId)) {
        const topicLimits = window.getTopicLimits(topicId);
        const message = `Limite de ${topicLimits.fieldsLimit} campos por tópico atingido. Este tópico já tem ${topicLimits.currentFieldsCount} campos.`;
        
        alertManager.warning(message);
        return null;
    }
    
    const topicContent = $(`.topic-content[data-topic-id="${topicId}"]`);
    const tbody = topicContent.find('tbody');
    
    // Remover linha de "nenhum campo" se existir
    tbody.find('tr:has(td[colspan="5"]):not(.add-field-trigger):not(.limit-reached-row)').remove();
    
    const newRow = `
        <tr class="border-b border-slate-700 hover:bg-slate-750 transition-colors duration-200 new-field" 
            data-topic-id="${topicId}">
            <td class="px-6 py-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="visibility-checkbox sr-only peer" checked>
                    <div class="relative w-11 h-6 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>                    
                </label>
            </td>
            <td class="px-6 py-4">
                <input type="text" name="key_name" class="key-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" placeholder="Nome da chave">
            </td>
            <td class="px-6 py-4">
                <input type="text" name="key_value" class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" placeholder="Valor">
            </td>
            <td class="px-6 py-4">
                <select name="field_type" class="type-select w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                    <option value="text" selected>Texto</option>
                    <option value="number">Número</option>
                    <option value="boolean">Booleano</option>
                </select>
            </td>
            <td class="px-6 py-4">
                <div class="flex space-x-2">
                    <button type="button" class="save-row p-2 text-teal-400 hover:text-teal-300 rounded-lg transition-colors duration-200" title="Salvar">
                        <i class="fas fa-save"></i>
                    </button>
                    <button type="button" class="remove-row p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors duration-200" title="Remover">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;

    const trigger = tbody.find('.add-field-trigger, .limit-reached-row');
    if (trigger.length) {
        trigger.before(newRow);
    } else {
        tbody.append(newRow);
    }
    
    const newRowElement = trigger.prev();
    newRowElement.find('.key-input').focus();

    return newRowElement;
}

// Função para remover campo (atualiza contador) - JÁ CORRETA
export function removeFieldCounter() {
    if (typeof window.currentFieldsCount !== 'undefined' && 
        typeof window.fieldsLimit !== 'undefined') {
        
        window.currentFieldsCount = Math.max(0, window.currentFieldsCount - 1);
        
        // Atualizar campos restantes se a variável existir
        if (typeof window.remainingFields !== 'undefined') {
            window.remainingFields = window.remainingFields + 1;
        }
        
        if (isUnlimitedPlan()) {
            window.canAddMoreFields = true;
        } else {
            // Para planos limitados, verificar se ainda pode adicionar
            window.canAddMoreFields = window.currentFieldsCount < window.fieldsLimit;
        }
        
        updateAddFieldButtons();
        updateLimitMessages();
        checkAndShowAddFieldOption();
    }
}

// Função para feedback visual de salvamento
export function showSaveFeedback(row) {
    row.addClass('bg-green-50 dark:bg-green-900/20');
    setTimeout(() => {
        row.removeClass('bg-green-50 dark:bg-green-900/20');
    }, 1000);
}

// Event listener para mudança de tipo (NOVA FUNÇÃO)
export function initializeTypeChangeListeners() {
    $(document).on('change', '.type-select', function() {
        const row = $(this).closest('tr');
        const selectedType = $(this).val();
        updateFieldInputType(row, selectedType);
    });
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
        
        // Para planos ilimitados, forçar canAddMoreFields = true
        if (window.fieldsLimit === 0) {
            window.canAddMoreFields = true;
        }
        
        updateAddFieldButtons();
        updateLimitMessages();
    }
    
    // Inicializar listeners de mudança de tipo
    initializeTypeChangeListeners();
});

export function handleFieldCreationError(xhr) {
    if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.error === 'limit_exceeded') {
        // Atualizar as variáveis globais para refletir o limite
        if (typeof window.fieldsLimit !== 'undefined') {
            window.currentFieldsCount = window.fieldsLimit;
            window.canAddMoreFields = false;
        }
        return true;
    }
    return false;
}

export function checkAndShowAddFieldOption() {    
    $('.topic-content').each(function() {
        const topicId = $(this).data('topic-id');
        const tbody = $(this).find('tbody');
        const addTrigger = tbody.find('.add-field-trigger');
        const limitRow = tbody.find('.limit-reached-row');
        
        if (!topicId) return;
        
        const topicLimits = window.getTopicLimits(topicId);
        if (!topicLimits) return;
        
        const canAdd = topicLimits.isUnlimited || topicLimits.canAddMoreFields;
        const hasExistingFields = tbody.find('tr[data-id]').length > 0;
        
        // Se pode adicionar e não tem botão de adicionar, criar um
        if (canAdd && addTrigger.length === 0) {
            // Remover mensagem de limite se existir
            if (limitRow.length > 0) {
                limitRow.remove();
            }
            
            // Criar botão de adicionar
            const addFieldText = window.translations?.workspace?.table?.add_field?.trigger || 'Adicionar campo';
            const newAddRow = `
                <tr class="add-field-trigger bg-slate-750 cursor-pointer hover:bg-slate-700 transition-colors duration-200" data-topic-id="${topicId}">
                    <td colspan="5" class="px-6 py-4 text-center text-teal-400">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            ${addFieldText}
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(newAddRow);
        }
        
        // Se não pode adicionar e não tem mensagem de limite, criar uma
        if (!canAdd && limitRow.length === 0) {
            // Remover botão de adicionar se existir
            if (addTrigger.length > 0) {
                addTrigger.remove();
            }
            
            // Criar mensagem de limite
            const newLimitRow = `
                <tr class="limit-reached-row bg-slate-750" data-topic-id="${topicId}">
                    <td colspan="5" class="px-6 py-4 text-center text-purple-400">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Limite de ${topicLimits.fieldsLimit} campos por tópico atingido.
                            <a href="/subscription/pricing" class="underline ml-1 text-white">
                                Faça upgrade
                            </a>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(newLimitRow);
        }
    });
}