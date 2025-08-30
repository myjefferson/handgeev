@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' , [
            'title' => 'Experiencias profissionais',
            'options' => [[
                'title' => 'Adicionar Experiência',
                'route' => route('dashboard.experiences.create')
            ]],
            'buttonViewJson' => [
                'active' => true,
                'route' => route('api.experiences')
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
