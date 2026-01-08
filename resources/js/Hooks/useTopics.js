// resources/js/Hooks/useTopics.js
import { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';

export function useTopics(initialTopics = []) {
    const [topics, setTopics] = useState(initialTopics);
    const [selectedTopicId, setSelectedTopicId] = useState(initialTopics[0]?.id || null);

    const createTopic = async (workspaceId, title) => {
        try {
            await router.post('/topics', {
                workspace_id: workspaceId,
                title: title,
                order: topics.length + 1
            }, {
                onSuccess: () => {
                    // A página será recarregada pelo Inertia
                }
            });
        } catch (error) {
            console.error('Erro ao criar tópico:', error);
            throw error;
        }
    };

    const updateTopic = async (topicId, data) => {
        try {
            await router.put(`/topics/${topicId}`, data);
        } catch (error) {
            console.error('Erro ao atualizar tópico:', error);
            throw error;
        }
    };

    const deleteTopic = async (topicId) => {
        try {
            await router.delete(`/topics/${topicId}`);
        } catch (error) {
            console.error('Erro ao deletar tópico:', error);
            throw error;
        }
    };

    return {
        topics,
        selectedTopicId,
        setSelectedTopicId,
        createTopic,
        updateTopic,
        deleteTopic
    };
}