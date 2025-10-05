@if(session('success'))
    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-400 mr-3"></i>
            <div>
                <p class="text-green-400 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
            <div>
                <p class="text-red-400 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="mb-6 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-400 mr-3"></i>
            <div>
                <p class="text-yellow-400 font-medium">{{ session('warning') }}</p>
            </div>
        </div>
    </div>
@endif