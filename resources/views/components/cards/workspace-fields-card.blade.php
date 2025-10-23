<div class="block md:hidden">
    <div class="p-4 space-y-4">
        @forelse($topic->fields as $field)
            <div class="bg-slate-750 rounded-lg border border-slate-600 p-4 space-y-3"
                data-id="{{ $field->id }}" data-topic-id="{{ $topic->id }}">
                
                <!-- Header com Visibilidade e Ações -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-slate-300">{{ __('workspace.table.headers.visibility') }}</span>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="visibility-checkbox sr-only peer" 
                                {{ $field->is_visible ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                        </label>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" class="save-row p-2 text-teal-400 hover:text-teal-300 rounded-lg transition-colors duration-200" title="{{ __('workspace.actions.save') }}">
                            <i class="fas fa-save"></i>
                        </button>
                        <button type="button" class="remove-row p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors duration-200" title="{{ __('workspace.actions.remove') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <!-- Campo Chave -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">
                        {{ __('workspace.table.headers.key') }}
                    </label>
                    <input type="text" name="key_name" value="{{ $field->key_name }}" 
                        class="key-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" 
                        placeholder="{{ __('workspace.fields.placeholders.key') }}">
                </div>

                <!-- Campo Valor -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">
                        {{ __('workspace.table.headers.value') }}
                    </label>
                    @if($field->type === 'boolean')
                        <select name="key_value" class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                            <option value="true" {{ $field->value === 'true' ? 'selected' : '' }}>{{ __('workspace.fields.boolean_options.true') }}</option>
                            <option value="false" {{ $field->value === 'false' ? 'selected' : '' }}>{{ __('workspace.fields.boolean_options.false') }}</option>
                        </select>
                    @else
                        <input type="{{ $field->type === 'number' ? 'number' : 'text' }}" 
                            name="key_value" 
                            value="{{ $field->value }}" 
                            class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" 
                            placeholder="{{ $field->type === 'number' ? __('workspace.fields.placeholders.number_value') : __('workspace.fields.placeholders.text_value') }}"
                            step="{{ $field->type === 'number' ? 'any' : '' }}">
                    @endif
                </div>

                <!-- Campo Tipo -->
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">
                        {{ __('workspace.table.headers.type') }}
                    </label>
                    <select name="field_type" class="type-select w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                        <option value="text" {{ $field->type === 'text' ? 'selected' : '' }}>{{ __('workspace.fields.types.text') }}</option>
                        @if(!auth()->user()->isFree())
                            <option value="number" {{ $field->type === 'number' ? 'selected' : '' }}>{{ __('workspace.fields.types.number') }}</option>
                            <option value="boolean" {{ $field->type === 'boolean' ? 'selected' : '' }}>{{ __('workspace.fields.types.boolean') }}</option>
                        @else
                            <option value="number" disabled class="text-gray-500 bg-slate-600">{{ __('workspace.fields.types.locked.number') }}</option>
                            <option value="boolean" disabled class="text-gray-500 bg-slate-600">{{ __('workspace.fields.types.locked.boolean') }}</option>
                        @endif
                    </select>
                    @if(auth()->user()->isFree())
                        <p class="text-xs text-purple-400 mt-2">
                            <i class="{{ __('workspace.fields.upgrade_message.icon') }} mr-1"></i>
                            {!! __('workspace.fields.upgrade_message.text', [
                                'upgrade_link' => '<a href="'.route('subscription.pricing').'" class="underline hover:text-purple-300">'.__('workspace.fields.upgrade_message.link').'</a>'
                            ]) !!}
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <i class="{{ __('workspace.table.empty.icon') }} text-2xl mb-2"></i>
                <p>{{ __('workspace.table.empty.message') }}</p>
            </div>
        @endforelse

        <!-- Card para adicionar novo campo -->
        @if($canAddMore || $topicLimits['isUnlimited'])
            <div class="add-field-trigger bg-slate-750 cursor-pointer hover:bg-slate-700 transition-colors duration-200 rounded-lg border border-slate-600 p-4 text-center"
                data-topic-id="{{ $topic->id }}">
                <div class="flex items-center justify-center text-teal-400">
                    <i class="fas fa-plus-circle mr-2"></i>
                    {{ __('workspace.table.add_field.trigger') }}
                </div>
            </div>
        @else
            <div class="limit-reached-row bg-slate-750 rounded-lg border border-slate-600 p-4 text-center text-purple-400">
                <div class="flex items-center justify-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm">
                        Limite de {{ $topicLimits['fieldsLimit'] }} campos por tópico atingido.
                        <a href="{{ route('subscription.pricing') }}" class="underline ml-1 text-white">
                            Faça upgrade
                        </a>
                    </span>
                </div>
            </div>
        @endif
    </div>
</div>