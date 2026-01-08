// resources/js/Hooks/useFields.js
import { useState } from 'react';
import { router } from '@inertiajs/react';

export function useFields(initialFields = []) {
    const [fields, setFields] = useState(initialFields);
    const [isLoading, setIsLoading] = useState(false);

    const createField = async (topicId, fieldData) => {
        setIsLoading(true);
        try {
            await router.post('/fields', {
                topic_id: topicId,
                ...fieldData
            });
            // O Inertia vai recarregar a página com os dados atualizados
        } catch (error) {
            console.error('Erro ao criar campo:', error);
            throw error;
        } finally {
            setIsLoading(false);
        }
    };

    const updateField = async (fieldId, fieldData) => {
        setIsLoading(true);
        try {
            await router.put(`/fields/${fieldId}`, fieldData);
        } catch (error) {
            console.error('Erro ao atualizar campo:', error);
            throw error;
        } finally {
            setIsLoading(false);
        }
    };

    const deleteField = async (fieldId) => {
        setIsLoading(true);
        try {
            await router.delete(`/fields/${fieldId}`);
        } catch (error) {
            console.error('Erro ao deletar campo:', error);
            throw error;
        } finally {
            setIsLoading(false);
        }
    };

    return {
        fields,
        isLoading,
        createField,
        updateField,
        deleteField
    };
}