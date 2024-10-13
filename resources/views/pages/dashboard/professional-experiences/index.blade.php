@extends('layout.template')

@section('content')
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
