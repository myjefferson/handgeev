@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' , [
            'title' => 'Experiencias profissionais',
            'options' => [[
                'title' => 'Adicionar Experiência',
                'route' => route('dashboard.experiences.create')
            ]],
            'buttonJson' => [
                'active' => true,
                'route' => route('api.experiences'),
                'primary_hash_api' => Auth::user()->primary_hash_api,
                'secondary_hash_api' => Auth::user()->secondary_hash_api,
            ]
        ])

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-6">
            @foreach ($experiences as $index => $experience)
                @include('components.card-experience', [
                    $index,
                    $experience
                ])
            @endforeach
        </div>
    </div>
@endsection
