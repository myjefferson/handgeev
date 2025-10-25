<div id="confirmModal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-slate-800 rounded-lg shadow border border-slate-700">
            <div class="p-4 md:p-5 text-center">
                <i class="fas fa-exclamation-triangle text-amber-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-normal text-white mb-5" id="modalMessage">Tem certeza que deseja alterar o status desta API?</h3>
                <div class="flex justify-center space-x-4">
                    <button type="button" id="confirmAction" class="py-2 px-4 text-sm font-medium text-white bg-teal-500 rounded-lg hover:bg-teal-600 focus:ring-4 focus:outline-none focus:ring-teal-300">
                        Confirmar
                    </button>
                    <button type="button" id="cancelAction" class="py-2 px-4 text-sm font-medium text-slate-400 bg-slate-700 rounded-lg hover:bg-slate-600 hover:text-white focus:ring-4 focus:outline-none focus:ring-slate-600">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>