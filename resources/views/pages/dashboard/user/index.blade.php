@extends('layout.template')

@section('content_dashboard')
    <div>
        @include('components.header' ,[
            'title' => 'Informações pessoais',
            'options' => [[
                'title' => 'Editar',
                'route' => route('dashboard.personal-data.edit', ['id' => $user->id])
                ]],
            'buttonJson' => [
                'active' => true,
                'route' => route('api.personal-data'),
                'primary_hash_api' => Auth::user()->primary_hash_api,
                'secondary_hash_api' => Auth::user()->secondary_hash_api,
            ]
        ])

        <hr class="h-px my-6 bg-gray-600 border-0"/>


        <session>
            <div class="text-xl font-medium">Pessoal</div>
            <div class="grid grid-cols-2 mt-4 space-y-4">
                <div>
                    <label class="text-sm text-gray-300">Nome</label>
                    <p class="font-medium">{{ $user->name ? $user->name : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Sobrenome</label>
                    <p class="font-medium">{{ $user->surname ? $user->surname : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Data de nascimento</label>
                    <p class="font-medium">{{$user->birthdate ? $user->birthdate : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Estado Civil</label>
                    <p class="font-medium">{{ $user->marital_status ? $user->marital_status : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Nacionalidade</label>
                    <p class="font-medium">{{$user->nationality ? $user->nationality : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Categoria CNH</label>
                    <p class="font-medium">{{$user->driver_license_category ? $user->driver_license_category : 'Não informado'}}</p>
                </div>
            </div>
            <div class="my-5">
                <div>
                    <label class="text-sm text-gray-300">Sobre mim</label>
                    <p class="font-medium">{{$user->about_me ? $user->about_me : 'Não informado'}}</p>
                </div>
            </div>
            <hr class="h-px my-6 bg-gray-600 border-0"/>
        </session>
        <session>
            <div class="text-xl font-medium">Endereço</div>
            <div class="grid grid-cols-2 mt-4 space-y-4">
                <div>
                    <label class="text-sm text-gray-300">CEP</label>
                    <p class="font-medium">{{$user->postal_code ? $user->postal_code : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Logradouro</label>
                    <p class="font-medium">{{$user->street ? $user->street : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Bairro</label>
                    <p class="font-medium">{{$user->neighborhood ? $user->neighborhood : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Cidade</label>
                    <p class="font-medium">{{$user->city ? $user->city : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Estado</label>
                    <p class="font-medium">{{$user->state ? $user->state : 'Não informado'}}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Complemento</label>
                    <p class="font-medium">{{$user->complement ? $user->complement : 'Não informado' }}</p>
                </div>
            </div>
            <hr class="h-px my-6 bg-gray-600 border-0"/>
        </session>
        <session>
            <div class="text-xl font-medium">Contato</div>

            <div class="grid grid-cols-2 mt-4 space-y-4">
                <div>
                    <label class="text-sm text-gray-300">E-mail</label>
                    <p class="font-medium">{{ $user->email ? $user->email : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Telefone</label>
                    <p class="font-medium">{{ $user->phone ? $user->phone : 'Não informado' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-300">Redes Sociais</label>
                    <p class="font-medium">{{ $user->social ? $user->social : 'Não informado' }}</p>
                </div>
            </div>
        </session>
    </div>
@endsection
