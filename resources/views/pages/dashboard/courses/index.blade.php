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
                'route' => route('api.courses'),
                'primary_hash_api' => Auth::user()->primary_hash_api,
                'secondary_hash_api' => Auth::user()->secondary_hash_api,
            ]
        ])
    </div>

    <div class="w-full flex items-center justify-center mt-7 gap-4">
        @forelse ($courses as $index => $course)
            @include('components.card-course', [$course, $index])
        @empty
            <p>Adicione acadêmicos e cursos</p>
        @endforelse
    </div>
@endsection
