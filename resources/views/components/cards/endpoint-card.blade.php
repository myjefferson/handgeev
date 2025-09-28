<div class="bg-slate-900 rounded-lg p-4 border border-slate-700">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
            <span class="px-2 py-1 text-xs font-mono rounded 
                {{ $endpoint['method'] === 'GET' ? 'bg-green-500/20 text-green-400' : '' }}
                {{ $endpoint['method'] === 'POST' ? 'bg-blue-500/20 text-blue-400' : '' }}
                {{ $endpoint['method'] === 'PUT' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                {{ $endpoint['method'] === 'DELETE' ? 'bg-red-500/20 text-red-400' : '' }}">
                {{ $endpoint['method'] }}
            </span>
            <code class="text-cyan-300 text-sm">{{ $endpoint['path'] }}</code>
        </div>
        <button onclick="copyToClipboard('{{ $endpoint['path'] }}')" class="text-slate-400 hover:text-cyan-400">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
            </svg>
        </button>
    </div>
    <p class="text-slate-400 text-sm mb-3">{{ $endpoint['description'] }}</p>
    
    @if(isset($endpoint['parameters']))
        <div class="mt-2">
            <span class="text-slate-500 text-xs uppercase font-semibold">Parâmetros:</span>
            <div class="flex flex-wrap gap-1 mt-1">
                @foreach($endpoint['parameters'] as $param)
                    <span class="px-2 py-1 bg-slate-800 text-slate-300 text-xs rounded">{{ $param }}</span>
                @endforeach
            </div>
        </div>
    @endif
</div>