@extends('layout.template')

@section('content_dashboard')
    <div class="bg-slate-800 p-3 rounded-xl">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-medium">Alterar projeto pessoal</h3>
        </div>
        <div>
            <form class="space-y-3 md:space-y-4" action="{{route('dashboard.projects.update', ['id' => $project->id])}}" method="POST">
                @csrf
                <div>
                    <label for="title" class="block mb-2 text-sm font-medium ">Nome do projeto</label>
                    <input type="text" name="title" id="title" value="{{ $project->title ? $project->title : '' }}" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="subtitle" class="block mb-2 text-sm font-medium ">Subtítulo</label>
                    <input type="text" name="subtitle" id="subtitle" value="{{ $project->subtitle ? $project->subtitle : '' }}" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>

                <div>
                    <label for="description" class="block mb-2 text-sm font-medium">Descrição</label>
                    <textarea
                        name="description"
                        id="description"
                        placeholder="Descreva um pouco sobre você"
                        class=" bg-slate-700 sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full h-40 min-h-28 max-h-96 p-3"
                    >{{ $project->description ? $project->description : '' }}</textarea>
                </div>
                <div>
                    <label for="technologies_used" class="block mb-2 text-sm font-medium ">Tecnologias utilizadas</label>
                    <input type="text" name="technologies_used" id="technologies_used" value="{{ $project->technologies_used ? $project->technologies_used : '' }}" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="start_date" class="block mb-2 text-sm font-medium ">Data de entrada</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $project->start_date ? $project->start_date : '' }}" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="status" class="block mb-2 text-sm font-medium ">Status</label>
                    <select name="status" id="status" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3">
                        <option value="">Selecione...</option>
                        <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Em desenvolvimento</option>
                        <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Finalizado</option>
                        <option value="archived"{{ $project->status == 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>

                <div>
                    <label for="end_date" class="block mb-2 text-sm font-medium ">Data de saida</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $project->end_date ? $project->end_date : '' }}" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="project_link" class="block mb-2 text-sm font-medium ">Link do Projeto / Site</label>
                    <input type="text" name="project_link" id="project_link" value="{{ $project->project_link ? $project->project_link : '' }}" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="git_repository_link" class="block mb-2 text-sm font-medium ">Link do repositório Git</label>
                    <input type="text" name="git_repository_link" id="git_repository_link" value="{{ $project->git_repository_link ? $project->git_repository_link : '' }}" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>

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
