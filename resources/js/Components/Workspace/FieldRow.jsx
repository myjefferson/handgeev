import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function FieldRow({ field, onSave, onDelete, auth, translations }) {
    const [isEditing, setIsEditing] = useState(field.is_new || false);
    const [formData, setFormData] = useState({
        key_name: field.key_name || '',
        value: field.value || '',
        type: field.type || 'text',
        is_visible: field.is_visible !== undefined ? field.is_visible : true
    });

    const { processing, post, put, delete: destroy } = useForm();

    const handleSave = async () => {
        try {
            await onSave(field.id, formData);
            setIsEditing(false);
        } catch (error) {
            console.error('Erro ao salvar campo:', error);
        }
    };

    const handleDelete = async () => {
        try {
            await onDelete(field.id);
        } catch (error) {
            console.error('Erro ao deletar campo:', error);
        }
    };

    const handleInputChange = (fieldName, value) => {
        setFormData(prev => ({
            ...prev,
            [fieldName]: value
        }));
    };

    const renderValueInput = () => {
        if (formData.type === 'boolean') {
            return (
                <select 
                    value={formData.value}
                    onChange={(e) => handleInputChange('value', e.target.value)}
                    className="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                >
                    <option value="true">{translations.workspace.fields.boolean_options.true}</option>
                    <option value="false">{translations.workspace.fields.boolean_options.false}</option>
                </select>
            );
        } else if (formData.type === 'number') {
            return (
                <input 
                    type="number"
                    value={formData.value}
                    onChange={(e) => handleInputChange('value', e.target.value)}
                    className="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                    placeholder={translations.workspace.fields.placeholders.number_value}
                    step="any"
                />
            );
        } else {
            return (
                <input 
                    type="text"
                    value={formData.value}
                    onChange={(e) => handleInputChange('value', e.target.value)}
                    className="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                    placeholder={translations.workspace.fields.placeholders.text_value}
                />
            );
        }
    };

    const renderTypeSelect = () => {
        const isFreeUser = auth.user.is_free; // Ajuste conforme sua lógica de planos

        return (
            <select 
                value={formData.type}
                onChange={(e) => handleInputChange('type', e.target.value)}
                className="type-select w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
            >
                <option value="text">{translations.workspace.fields.types.text}</option>
                {!isFreeUser ? (
                    <>
                        <option value="number">{translations.workspace.fields.types.number}</option>
                        <option value="boolean">{translations.workspace.fields.types.boolean}</option>
                    </>
                ) : (
                    <>
                        <option value="number" disabled className="text-gray-500 bg-slate-600">
                            {translations.workspace.fields.types.locked.number}
                        </option>
                        <option value="boolean" disabled className="text-gray-500 bg-slate-600">
                            {translations.workspace.fields.types.locked.boolean}
                        </option>
                    </>
                )}
            </select>
        );
    };

    return (
        <tr className="border-b border-slate-700 hover:bg-slate-750 transition-colors duration-200" 
            data-id={field.id} 
            data-topic-id={field.topic_id}>
            
            {/* Visibility Toggle */}
            <td className="px-6 py-4">
                <label className="inline-flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        className="visibility-checkbox sr-only peer" 
                        checked={formData.is_visible}
                        onChange={(e) => handleInputChange('is_visible', e.target.checked)}
                    />
                    <div className="relative w-11 h-6 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                </label>
            </td>

            {/* Key Name */}
            <td className="px-6 py-4">
                <input 
                    type="text" 
                    value={formData.key_name}
                    onChange={(e) => handleInputChange('key_name', e.target.value)}
                    className="key-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                    placeholder={translations.workspace.fields.placeholders.key}
                />
            </td>

            {/* Value */}
            <td className="px-6 py-4">
                {renderValueInput()}
            </td>

            {/* Type */}
            <td className="px-6 py-4">
                {renderTypeSelect()}
                {auth.user.is_free && (
                    <p className="text-xs text-purple-400 mt-1">
                        <i className={translations.workspace.fields.upgrade_message.icon + " mr-1"}></i>
                        {translations.workspace.fields.upgrade_message.text.replace(
                            '{upgrade_link}', 
                            `<a href="${route('subscription.pricing')}" class="underline hover:text-purple-300">${translations.workspace.fields.upgrade_message.link}</a>`
                        )}
                    </p>
                )}
            </td>

            {/* Actions */}
            <td className="px-6 py-4">
                <div className="flex space-x-2">
                    <button 
                        onClick={handleSave}
                        disabled={processing}
                        className="save-row p-2 text-teal-400 hover:text-teal-300 rounded-lg transition-colors duration-200 disabled:opacity-50"
                        title={translations.workspace.actions.save}
                    >
                        <i className="fas fa-save"></i>
                    </button>
                    <button 
                        onClick={handleDelete}
                        className="remove-row p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors duration-200"
                        title={translations.workspace.actions.remove}
                    >
                        <i className="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    );
}