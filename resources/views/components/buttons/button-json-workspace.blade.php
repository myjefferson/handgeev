<div class="flex items-center">
    <input type="hidden" id="global-hash" value="{{ Auth::user()->global_hash_api }}">

    <button type="button" id="view-json-btn" class="flex items-center font-medium bg-cyan-400 text-slate-950 rounded-xl py-2 px-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24"><path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/></svg>
        JSON
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Encontra o botão pelo ID
        const viewJsonBtn = document.getElementById('view-json-btn');

        if (viewJsonBtn) {
            viewJsonBtn.addEventListener('click', function() {
                // Obtém os valores das chaves de API dos campos hidden
                const globalHash = document.getElementById('global-hash').value;

                // URL da sua rota, ajustável conforme a sua aplicação
                const jsonUrl = "{{ route('api.workspace', ['id' => $workspace->id]) }}";

                // Usando $.ajax para fazer a requisição GET
                $.ajax({
                    url: jsonUrl,
                    method: 'GET',
                    headers: {
                        'X-Primary-Hash-Api': globalHash,
                    },
                    success: function(data) {
                        // Converte o objeto JSON recebido para uma string formatada
                        const jsonString = JSON.stringify(data, null, 2);
                        
                        // Cria um objeto Blob com o tipo MIME 'application/json'
                        const jsonBlob = new Blob([jsonString], { type: 'application/json' });
                        
                        // Cria uma URL temporária para o Blob
                        const jsonUrlBlob = URL.createObjectURL(jsonBlob);
                        
                        // Abre a nova janela com a URL do Blob
                        const newWindow = window.open(jsonUrlBlob, '_blank');
                        
                        if (newWindow) {
                            newWindow.focus();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao buscar JSON:', xhr.responseJSON || xhr.responseText);
                        // Exibe uma mensagem de erro na UI (sem usar alert())
                        const errorMessage = xhr.responseJSON?.message || 'Erro ao carregar o JSON.';
                        const errorDiv = document.createElement('div');
                        errorDiv.textContent = errorMessage;
                        errorDiv.style.cssText = "color: red; margin-top: 10px; font-weight: bold;";
                        viewJsonBtn.parentElement.appendChild(errorDiv);
                    }
                });
            });
        }
    });
</script>
