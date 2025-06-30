@extends('layout.template')

@php
    $isEdit = false;
    if(isset($project->id)){
        $isEdit = true;
    }
@endphp

@section('content_dashboard')
    <div class="bg-slate-800 p-3 rounded-xl">
        <div class="text-left">
            <button type="submit" onclick="window.history.back()" class="flex items-center w-max text-teal-400 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800 mb-4">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M244 400L100 256l144-144M120 256h292"/></svg>
                Voltar
            </button>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-medium">
                {{ $isEdit ? "Editar projeto pessoal" : "Adicionar projeto pessoal" }}
            </h3>
        </div>
        <div>
            @isset($project->id)
                <form class="space-y-3 md:space-y-4" action="{{route('dashboard.projects.update', ['id' => $project->id])}}" method="POST">
            @else
                <form class="space-y-3 md:space-y-4" action="{{route('dashboard.projects.store')}}" method="POST">
            @endisset

                @csrf
                <div>
                    <div class="flex justify-between items-center">
                        <label for="title" class="block mb-2 text-sm font-medium ">Imagens do projeto</label>
                        <button type="button" id="buttonAddInputImage" onclick="addInputUrlImage()" class="w-max text-slate-900 bg-teal-400 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-4 py-1 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800 mb-4">Adicionar campo URL</button>
                    </div>
                    <div class="content-input-images">
                        <input type="text" name="title" id="title" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="Cole a URL da imagem do seu projeto">
                    </div>
                </div>
                <div>
                    <label for="title" class="block mb-2 text-sm font-medium ">Nome do projeto</label>
                    <input value="{{ $isEdit ? $project->title : "" }}" type="text" name="title" id="title" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="subtitle" class="block mb-2 text-sm font-medium ">Subtítulo</label>
                    <input value="{{ $isEdit ? $project->subtitle : "" }}" type="text" name="subtitle" id="subtitle" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>

                <div>
                    <label for="description" class="block mb-2 text-sm font-medium">Descrição</label>
                    <textarea
                        name="description"
                        id="description"
                        placeholder="Descreva um pouco sobre você"
                        class=" bg-slate-700 sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full h-40 min-h-28 max-h-96 p-3"
                    >{{ $isEdit ? $project->description : "" }}</textarea>
                </div>
                <div>
                    <label for="technologies_used" class="block mb-2 text-sm font-medium ">Tecnologias utilizadas</label>
                    <input value="{{ $isEdit ? $project->technologies_used : "" }}" type="text" name="technologies_used" id="technologies_used" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="start_date" class="block mb-2 text-sm font-medium ">Data de entrada</label>
                    <input value="{{ $isEdit ? $project->start_date : "" }}" type="date" name="start_date" id="start_date" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="status" class="block mb-2 text-sm font-medium ">Status</label>
                    <select name="status" id="status" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3">
                        <option value="">Selecione...</option>
                        <option value="active" {{ ($isEdit && $project->status == "active") ? 'selected' : '' }}>Em desenvolvimento</option>
                        <option value="completed" {{ ($isEdit && $project->status == "completed") ? 'selected' : '' }}>Finalizado</option>
                        <option value="archived" {{ ($isEdit && $project->status == "archived") ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>

                <div>
                    <label for="end_date" class="block mb-2 text-sm font-medium ">Data de saida</label>
                    <input value="{{ $isEdit ? $project->end_date : "" }}" type="date" name="end_date" id="end_date" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com">
                </div>
                <div>
                    <label for="project_link" class="block mb-2 text-sm font-medium ">Link do Projeto / Site</label>
                    <input value="{{ $isEdit ? $project->project_link : "" }}" type="text" name="project_link" id="project_link" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com">
                </div>
                <div>
                    <label for="git_repository_link" class="block mb-2 text-sm font-medium ">Link do repositório Git</label>
                    <input value="{{ $isEdit ? $project->git_repository_link : "" }}" type="text" name="git_repository_link" id="git_repository_link" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com">
                </div>

                {{--<div class="flex items-center justify-between">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4  -gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                        </div>
                            <div class="ml-3 text-sm">
                            <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
                        </div>
                    </div>
                    <a href="#" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">Forgot password?</a>
                </div>--}}
                <div class="text-center">
                    <button type="submit" class="w-max text-slate-900 bg-teal-400 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-5 py-3 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800 mt-4">Adicionar</button>
                </div>
                {{-- <p class="text-sm text-center ont-light text-gray-500 dark:text-gray-400">
                    Don’t have an account yet? <a href="#" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Sign up</a>
                </p> --}}
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const MAX_INPUTS = 5
        function countInputURL(){
            var numberOfInputs = $('.content-input-images').find('input').length 
            $('#buttonAddInputImage').prop('disabled', numberOfInputs >= MAX_INPUTS)
        }

        function addInputUrlImage(){
            $('.content-input-images').append(`
                <div class="flex items-center mt-3 space-x-3 input-section">
                    <input type="text" name="title" id="title" class="bg-slate-600 sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="Cole a URL da imagem do seu projeto">
                    <button onclick="removeInput(this)" type="button" class="w-max text-slate-900 bg-red-400 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-4 py-2.5 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800">Remover</button>
                </div>
            `)
            countInputURL()
        }

        function removeInput(element){
            element.closest('.input-section').remove()
            countInputURL()
        }
    </script>
@endpush
