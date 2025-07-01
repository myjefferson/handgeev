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
                'route' => route('api.projects'),
                'primary_hash_api' => Auth::user()->primary_hash_api,
                'secondary_hash_api' => Auth::user()->secondary_hash_api,
            ]
        ])
    </div>

    <div class="w-full items-center justify-center grid md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 mt-7 gap-4">
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
