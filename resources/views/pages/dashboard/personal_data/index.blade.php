@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' ,[
            'title' => 'Informações pessoais',
            'options' => [[
                'title' => 'Editar',
                'route' => route('dashboard.personal-data.edit', ['id' => $personal_data->id])
                ]],
            'buttonJson' => [
                'active' => true,
                'route' => route('api.personal-data', ['userId' => $personal_data->id])
            ]
        ])

        <hr class="h-px my-6 bg-gray-600 border-0"/>


        <session>
            <div class="text-xl font-medium">Pessoal</div>
            <div class="grid grid-cols-2 mt-4 space-y-4">
                <div>
                    <label class="text-sm text-gray-300">Nome</label>
                    <p class="font-medium">{{ $personal_data->name ? $personal_data->name : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Sobrenome</label>
                    <p class="font-medium">{{ $personal_data->surname ? $personal_data->surname : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Data de nascimento</label>
                    <p class="font-medium">{{$personal_data->birthdate ? $personal_data->birthdate : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Estado Civil</label>
                    <p class="font-medium">{{ $personal_data->marital_status ? $personal_data->marital_status : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Nacionalidade</label>
                    <p class="font-medium">{{$personal_data->nationality ? $personal_data->nationality : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Categoria CNH</label>
                    <p class="font-medium">{{$personal_data->driver_license_category ? $personal_data->driver_license_category : 'Não informado'}}</p>
                </div>
            </div>
            <div class="my-5">
                <div>
                    <label class="text-sm text-gray-300">Sobre mim</label>
                    <p class="font-medium">{{$personal_data->about_us ? $personal_data->about_us: 'Não informado'}}</p>
                </div>
            </div>
            <hr class="h-px my-6 bg-gray-600 border-0"/>
        </session>
        <session>
            <div class="text-xl font-medium">Endereço</div>
            <div class="grid grid-cols-2 mt-4 space-y-4">
                <div>
                    <label class="text-sm text-gray-300">CEP</label>
                    <p class="font-medium">{{$personal_data->postal_code ? $personal_data->postal_code : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Logradouro</label>
                    <p class="font-medium">{{$personal_data->street ? $personal_data->street : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Bairro</label>
                    <p class="font-medium">{{$personal_data->neighborhood ? $personal_data->neighborhood : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Cidade</label>
                    <p class="font-medium">{{$personal_data->city ? $personal_data->city : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Estado</label>
                    <p class="font-medium">{{$personal_data->state ? $personal_data->state : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Complemento</label>
                    <p class="font-medium">{{$personal_data->complement ? $personal_data->complement : 'Não informado' }}</p>
                </div>
            </div>
            <hr class="h-px my-6 bg-gray-600 border-0"/>
        </session>
        <session>
            <div class="text-xl font-medium">Contato</div>

            <div class="grid grid-cols-2 mt-4 space-y-4">
                <div>
                    <label class="text-sm text-gray-300">E-mail</label>
                    <p class="font-medium">{{ $personal_data->email ? $personal_data->email : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Telefone</label>
                    <p class="font-medium">{{ $personal_data->phone ? $personal_data->phone : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Redes Sociais</label>
                    <p class="font-medium">{{ $personal_data->social ? $personal_data->social : 'Não informado' }}</p>
                </div>
            </div>
        </session>
    </div>
@endsection
