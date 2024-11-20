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
                'route' => route('api.courses', ['userId' => Auth::user()->id])
            ]
        ])
    </div>

    <div class="w-full flex items-center justify-center mt-7 gap-4">
        @forelse ($projects as $project)
            @include('components.card-project', ['project' => $project])
        @empty
            <p>Adicione seus projetos</p>
        @endforelse
    </div>
@endsection
