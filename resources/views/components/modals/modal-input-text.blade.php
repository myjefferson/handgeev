

<!-- Main modal -->
<div id="edit-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-description">
                    New name from Workspace
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="edit-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form id="edit-form" method="POST" action="" class="p-4 md:p-5">
                @csrf
                @method('PUT')
                <input type="hidden" name="type_workspace_id" id="edit-type-id-input">
                <input type="hidden" name="is_published" id="edit-is-published-input">
                <div class="col-span-2 mb-6">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                    <input type="text" name="title" id="title-edit" value="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="My New Workspace" required="">
                </div>
                <div class="flex w-full justify-end">
                    <button type="button" id="cancel-edit" class="py-2.5 px-5 mr-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-teal-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancelar</button>
                    <button type="submit" class="text-teal-950 inline-flex items-center bg-teal-400 hover:bg-teal-800 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg px-5 py-2.5 text-center dark:bg-teal-400 dark:hover:bg-teal-600 dark:focus:ring-teal-800">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('edit-modal');
        const modalDescription = document.getElementById('modal-description');
        const editForm = document.getElementById('edit-form');
        const cancelButton = document.getElementById('cancel-edit');
        const titleEdit = document.getElementById('title-edit');
        const typeIdInput = document.getElementById('edit-type-id-input');
        const isPublishedInput = document.getElementById('edit-is-published-input');
        
        // Adiciona evento de clique a todos os botões de edit
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const route = this.getAttribute('data-route');
                const typeId = this.getAttribute('data-type-id');
                const isPublished = this.getAttribute('data-is-published');
                
                // Atualiza o modal com os dados específicos
                modalDescription.textContent = `New name from Workspace`;

                editForm.action = route;
                typeIdInput.value = typeId;
                isPublishedInput.value = isPublished;
                titleEdit.value = title;
                
                // Exibe o modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });
        
        // Fecha o modal ao clicar no cancelar
        cancelButton.addEventListener('click', function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
        
        // Fecha o modal ao clicar fora dele
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });
</script>