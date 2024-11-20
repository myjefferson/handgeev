@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' ,[
            'title' => 'Configuração'
        ])
    </div>
    <session>
        <hr class="h-px my-6 bg-gray-600 border-0"/>
        <div class="text-xl font-medium">Segurança</div>
            <div class="mt-4 space-y-4">
                <div>
                    <p class="text-sm text-gray-300">Senha</p>
                    <button class="font-medium bg-teal-400 hover:bg-teal-500 text-slate-900 py-2 px-4 mt-3 rounded-xl">Alterar senha</button>
                </div>
            </div>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="text-sm text-gray-300">API code secret</label>
                    <p class="font-medium">{{ $settings->hash_api ? $settings->hash_api : 'Não gerado' }}</p>
                    <button class="flex items-center font-medium bg-teal-400 hover:bg-teal-500 text-slate-900 py-2 px-4 mt-3 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1" width="1.4em" height="1.4em" viewBox="0 0 24 24"><path fill="currentColor" d="M4 20v-2h2.75l-.4-.35q-1.225-1.225-1.787-2.662T4 12.05q0-2.775 1.663-4.937T10 4.25v2.1Q8.2 7 7.1 8.563T6 12.05q0 1.125.425 2.188T7.75 16.2l.25.25V14h2v6zm10-.25v-2.1q1.8-.65 2.9-2.212T18 11.95q0-1.125-.425-2.187T16.25 7.8L16 7.55V10h-2V4h6v2h-2.75l.4.35q1.225 1.225 1.788 2.663T20 11.95q0 2.775-1.662 4.938T14 19.75"/></svg>
                        <span>Gerar novo código</span>
                    </button>
                </div>
            </div>
    </session>
@endsection
