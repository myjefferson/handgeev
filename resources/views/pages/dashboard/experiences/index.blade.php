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
                'route' => route('api.experiences', ['userId' => Auth::user()->id])
            ]
        ])

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-6">
            @foreach ($experiences as $experience)
                @include('components.card-experiences', ['experience' => $experience])
            @endforeach
        </div>
    </div>
@endsection
