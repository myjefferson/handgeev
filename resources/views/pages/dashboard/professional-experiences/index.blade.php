@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' , [
            'title' => 'Experiencias profissionais',
            'options' => [[
                'title' => 'Adicionar Experiência',
                'link' => '/adicionar'
            ]]
        ])
    </div>
@endsection
