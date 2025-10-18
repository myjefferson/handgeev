{{-- resources/views/components/alerts/alert.blade.php --}}
@php
    $messages = [
        'status' => [
            'bg' => 'bg-blue-500/10',
            'border' => 'border-blue-500/20',
            'text' => 'text-blue-400',
            'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
        ],
        'success' => [
            'bg' => 'bg-green-500/10',
            'border' => 'border-green-500/20',
            'text' => 'text-green-400',
            'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
        ],
        'error' => [
            'bg' => 'bg-red-500/10',
            'border' => 'border-red-500/20',
            'text' => 'text-red-400',
            'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
        ],
        'warning' => [
            'bg' => 'bg-yellow-500/10',
            'border' => 'border-yellow-500/20',
            'text' => 'text-yellow-400',
            'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>'
        ],
        'info' => [
            'bg' => 'bg-cyan-500/10',
            'border' => 'border-cyan-500/20',
            'text' => 'text-cyan-400',
            'icon' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
        ]
    ];
@endphp

<div id="flash-messages">
    {{-- MENSAGENS DE SESSION (status, success, etc) --}}
    @foreach($messages as $type => $styles)
        @if(session($type))
            <div class="flash-message mb-4 p-4 {{ $styles['bg'] }} {{ $styles['border'] }} rounded-lg {{ $styles['text'] }} transition-all duration-300 ease-in-out"
                 data-type="{{ $type }}"
                 data-auto-hide="true">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            {!! $styles['icon'] !!}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">
                                @if(is_array(session($type)))
                                    {{ implode(', ', array_filter(session($type), 'is_string')) }}
                                @else
                                    {{ session($type) }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            class="flash-close flex-shrink-0 ml-3 hover:opacity-70 transition-opacity focus:outline-none"
                            onclick="closeFlashMessage(this)">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    @endforeach

    {{-- MENSAGENS DE ERRO DE VALIDAÇÃO --}}
    @if($errors->any())
        <div class="flash-message mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 transition-all duration-300 ease-in-out"
             data-type="error"
             data-auto-hide="false"> {{-- Não fecha automaticamente erros de validação --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">
                            @if($errors->count() > 1)
                                Existem {{ $errors->count() }} erros no formulário:
                            @else
                                {{ $errors->first() }}
                            @endif
                        </p>
                        @if($errors->count() > 1)
                            <ul class="mt-2 text-sm list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <button type="button" 
                        class="flash-close flex-shrink-0 ml-3 hover:opacity-70 transition-opacity focus:outline-none"
                        onclick="closeFlashMessage(this)">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>

<script>
    function closeFlashMessage(button) {
        const message = button.closest('.flash-message');
        if (message) {
            message.style.opacity = '0';
            message.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (message.parentNode) {
                    message.remove();
                }
            }, 300);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.querySelectorAll('.flash-message[data-auto-hide="true"]');
        
        flashMessages.forEach(message => {
            // Auto-hide após 5 segundos
            const autoHide = setTimeout(() => {
                hideMessage(message);
            }, 5000);

            // Pausar auto-hide quando o mouse estiver sobre a mensagem
            message.addEventListener('mouseenter', () => {
                clearTimeout(autoHide);
            });

            // Retomar auto-hide quando o mouse sair (mais 3 segundos)
            message.addEventListener('mouseleave', () => {
                setTimeout(() => {
                    hideMessage(message);
                }, 3000);
            });
        });

        function hideMessage(message) {
            if (message.parentNode) {
                message.style.opacity = '0';
                message.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (message.parentNode) {
                        message.remove();
                    }
                }, 300);
            }
        }
    });
</script>