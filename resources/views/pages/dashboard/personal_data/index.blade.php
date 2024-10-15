@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' ,[
            'title' => 'Informações pessoais',
            'options' => [[
                'title' => 'Editar',
                'link' => route('dashboard.personal-data.edit', ['id' => $personal_data->id])
            ]]
        ])

        <div class="grid grid-cols-2 mt-4 space-y-4">
            <div>
                <label class="text-sm text-gray-300">Nome</label>
                <p class="font-medium">{{$personal_data->name ? $personal_data->name : 'Não informado'}}</p>
            </div>
            <div>
                <label class="text-sm text-gray-300">Sobrenome</label>
                <p class="font-medium">{{$personal_data->surname ? $personal_data->surname : 'Não informado'}}</p>
            </div>
            <div>
                <label class="text-sm text-gray-300">E-mail</label>
                <p class="font-medium">{{$personal_data->email ? $personal_data->email : 'Não informado'}}</p>
            </div>
            <div>
                <label class="text-sm text-gray-300">Sobrenome</label>
                <p class="font-medium">{{$personal_data->surname}}</p>
            </div>
            <div>
                <label class="text-sm text-gray-300">Sobrenome</label>
                <p class="font-medium">{{$personal_data->surname}}</p>
            </div>
            <div>
                <label class="text-sm text-gray-300">Sobrenome</label>
                <p class="font-medium">{{$personal_data->surname}}</p>
            </div>
        </div>
    </div>
@endsection
