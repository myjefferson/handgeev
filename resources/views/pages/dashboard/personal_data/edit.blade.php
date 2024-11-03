@extends('layout.template')

@php
    $marital_status = [
        'Solteiro',
        'Casado',
        'Divorciado',
        'Viúvo',
    ]
@endphp

@section('content_dashboard')
    <div class="bg-slate-800 p-3 rounded-xl">
        <div class="flex justify-between items-center">
            <h3 class="text-2xl font-medium">Editar Informações Pessoais</h3>
        </div>
        <div>
            <form action="{{route('dashboard.personal-data.update', ['id' => Auth::user()->id])}}" method="POST">
                @csrf
                @method('PUT')

                <section class="space-y-3 md:space-y-4">
                    <hr class="h-px my-6 bg-gray-600 border-0"/>
                    <div class="text-xl font-medium mb-5">Pessoal</div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block mb-2 text-sm font-medium ">Nome</label>
                                <input type="text" value="{{$personal_data->name ? $personal_data->name : ''}}" name="name" id="name" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                            </div>
                            <div>
                                <label for="surname" class="block mb-2 text-sm font-medium ">Sobrenome</label>
                                <input type="text" value="{{$personal_data->surname ? $personal_data->surname : ''}}" name="surname" id="surname" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="Ex: Carvalho">
                            </div>
                        </div>
                        <div>
                            <label for="birthdate" class="block mb-2 text-sm font-medium ">Data de nascimento</label>
                            <input type="date" name="birthdate" id="birthdate" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com">
                        </div>
                        <div>
                            <label for="marital_status" class="block mb-2 text-sm font-medium ">Estado civil</label>
                            <select name="marital_status" id="marital_status" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3">
                                <option value="">Selecione...</option>
                                @foreach ($marital_status as $status)
                                    <option value="{{$status}}" {{ $status == $personal_data->marital_status ? 'selected' : '' }}> {{$status}} </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="about_me" class="block mb-2 text-sm font-medium">Sobre mim</label>
                            <textarea
                                name="about_me"
                                id="about_me"
                                placeholder="Descreva um pouco sobre você"
                                class=" bg-slate-700 sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full h-40 min-h-28 max-h-96 p-3"
                            >{{$personal_data->about_me ? $personal_data->about_me : ''}}</textarea>
                        </div>
                    </section>

                    <section class="space-y-3 md:space-y-4">
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium ">Email</label>
                            <input type="email" value="{{$personal_data->email ? $personal_data->email : ''}}" name="email" id="email" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com">
                        </div>
                        <div>
                            <label for="personal_site" class="block mb-2 text-sm font-medium ">Site Pessoal</label>
                            <input type="text" value="{{$personal_data->personal_site ? $personal_data->personal_site : ''}}" name="personal_site" id="personal_site" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="">
                        </div>
                        <div>
                            <label for="social" class="block mb-2 text-sm font-medium ">Redes Sociais</label>
                            <input type="text" name="social" id="social" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="">
                        </div>
                        <div>
                            <label for="postal_code" class="block mb-2 text-sm font-medium ">Código Postal</label>
                            <input type="text" value="{{$personal_data->postal_code ? $personal_data->postal_code : ''}}" name="postal_code" id="postal_code" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="000000-000">
                        </div>
                        <div>
                            <label for="street" class="block mb-2 text-sm font-medium ">Rua</label>
                            <input type="text" value="{{$personal_data->street ? $personal_data->street : ''}}" name="street" id="street" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="Rua Alagoas">
                        </div>
                        <div>
                            <label for="complement" class="block mb-2 text-sm font-medium ">Complemento</label>
                            <input type="text" value="{{$personal_data->complement ? $personal_data->complement : ''}}" name="complement" id="complement" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="">
                        </div>
                        <div>
                            <label for="neighborhood" class="block mb-2 text-sm font-medium ">Bairro</label>
                            <input type="text" value="{{$personal_data->neighborhood ? $personal_data->neighborhood : ''}}" name="neighborhood" id="neighborhood" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="">
                        </div>
                        <div>
                            <label for="city" class="block mb-2 text-sm font-medium ">Cidade</label>
                            <input type="text" value="{{$personal_data->city ? $personal_data->city : ''}}" name="city" id="city" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="">
                        </div>
                        <div>
                            <label for="state" class="block mb-2 text-sm font-medium ">Estado</label>
                            <input type="text" value="{{$personal_data->state ? $personal_data->state : ''}}" name="state" id="state" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="">
                        </div>
                        <div>
                            <label class="block text-sm font-medium ">Resumo/ Currículo</label>
                            <div class="flex gap-2">
                                <button type="submit" class="w-full text-white bg-sky-600 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-5 py-3 text-center mt-2">Escolher</button>
                                <input type="file" class="hidden">
                                <button type="submit" class="w-full text-white bg-slate-600 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-5 py-3 text-center mt-2">Download</button>
                            </div>
                        </div>
                    </section>
                {{--<div class="flex items-center justify-between">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4  -gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800" required="">
                        </div>
                            <div class="ml-3 text-sm">
                            <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
                        </div>
                    </div>
                    <a href="#" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">Forgot password?</a>
                </div>--}}
                <div class="text-center">
                    <button type="submit" class="w-max text-slate-900 bg-teal-400 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-5 py-3 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800 mt-4">Salvar alterações</button>
                </div>
                {{-- <p class="text-sm text-center ont-light text-gray-500 dark:text-gray-400">
                    Don’t have an account yet? <a href="#" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Sign up</a>
                </p> --}}
            </form>
        </div>
    </div>
@endsection

@push('scripts')

@endpush
