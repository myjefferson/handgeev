@extends('layout.template')

@section('content_dashboard')
    <div class="bg-slate-800 p-3 rounded-xl">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-medium">Adicionar experiência profissional</h3>
        </div>
        <div>
            <form class="space-y-3 md:space-y-4" action="{{route('dashboard.experiences.store')}}" method="POST">
                @csrf
                <div>
                    <label for="enterprise" class="block mb-2 text-sm font-medium ">Empresa</label>
                    <input type="text" name="enterprise" id="enterprise" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required>
                </div>
                <div>
                    <label for="responsibility" class="block mb-2 text-sm font-medium ">Cargo</label>
                    <input type="text" name="responsibility" id="responsibility" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required>
                </div>
                <div>
                    <label for="description" class="block mb-2 text-sm font-medium">Descrição</label>
                    <textarea
                        name="description"
                        id="description"
                        placeholder="Descreva um pouco sobre você"
                        class=" bg-slate-700 sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full h-40 min-h-28 max-h-96 p-3"
                    ></textarea>
                </div>
                <div>
                    <label for="technologies_used" class="block mb-2 text-sm font-medium ">Tecnologias utilizadas</label>
                    <input type="text" name="technologies_used" id="technologies_used" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required>
                </div>
                <div>
                    <label for="start_date" class="block mb-2 text-sm font-medium ">Data de entrada</label>
                    <input type="date" name="start_date" id="start_date" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required>
                </div>

                <div>
                    <label for="end_date" class="block mb-2 text-sm font-medium ">Data de saida</label>
                    <input type="date" name="end_date" id="end_date" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required>
                </div>

                {{--<div class="flex items-center justify-between">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4  -gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800" required>
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
