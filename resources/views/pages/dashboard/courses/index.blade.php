@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' ,[
            'title' => 'Acadêmico e cursos'
        ])
    </div>

    <div class="w-full flex items-center justify-center">
        Adicione acadêmicos e cursos
    </div>
@endsection
