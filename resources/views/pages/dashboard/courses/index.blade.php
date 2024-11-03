@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' ,[
            'title' => 'Acadêmico e cursos',
            'options' => [[
                'route' => route('dashboard.courses.create'),
                'title' => 'Adicionar'
            ]],
            'buttonJson' => [
                'active' => true,
                'route' => ''
            ]
        ])
    </div>

    <div class="w-full flex items-center justify-center">
        Adicione acadêmicos e cursos
    </div>
@endsection
