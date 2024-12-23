@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' ,[
            'title' => 'Projetos pessoais',
            'options' => [[
                'route' => route('dashboard.projects.create'),
                'title' => 'Adicionar'
            ]],
            'buttonJson' => [
                'active' => true,
                'route' => route('api.courses'),
                'primary_hash_api' => Auth::user()->primary_hash_api,
                'secondary_hash_api' => Auth::user()->secondary_hash_api,
            ]
        ])
    </div>

    <div class="w-full flex items-center justify-center mt-7 gap-4">
        @forelse ($projects as $index => $project)
            @include('components.card-project', [
                $project,
                $index
            ])
        @empty
            <p>Adicione seus projetos</p>
        @endforelse
    </div>
@endsection
