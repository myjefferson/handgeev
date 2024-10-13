@extends('layout.template')

@section('content')
    <div class="bg-slate-800 p-3 rounded-xl">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-medium">Editar projeto pessoal</h3>
        </div>
        <div>
            <form class="space-y-3 md:space-y-4" action="{{route('login.store')}}" method="POST">
                @csrf
                <div>
                    <label for="description" class="block mb-2 text-sm font-medium">Descrição</label>
                    <textarea
                        name="description"
                        id="description"
                        placeholder="Descreva um pouco sobre você"
                        class=" bg-slate-700 sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full h-40 min-h-28 max-h-96 p-3"
                    >
                    </textarea>
                </div>
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium ">Email</label>
                    <input type="email" name="email" id="email" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="personal_site" class="block mb-2 text-sm font-medium ">Site Pessoal</label>
                    <input type="text" name="personal_site" id="personal_site" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="linkedin" class="block mb-2 text-sm font-medium ">LinkedIn</label>
                    <input type="text" name="linkedin" id="linkedin" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="github" class="block mb-2 text-sm font-medium ">GitHub</label>
                    <input type="text" name="github" id="github" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>

                <div>
                    <label for="instagram" class="block mb-2 text-sm font-medium ">Instagram</label>
                    <input type="text" name="instagram" id="instagram" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="behance" class="block mb-2 text-sm font-medium ">Behance</label>
                    <input type="text" name="behance" id="behance" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label for="dev_community" class="block mb-2 text-sm font-medium ">DEV Community</label>
                    <input type="text" name="dev_community" id="dev_community" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                </div>
                <div>
                    <label class="block text-sm font-medium ">Resumo/ Currículo</label>
                    <div class="flex gap-2">
                        <button type="submit" class="w-full text-white bg-sky-600 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-5 py-3 text-center mt-2">Escolher</button>
                        <input type="file" class="hidden">
                        <button type="submit" class="w-full text-white bg-slate-600 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-5 py-3 text-center mt-2">Download</button>
                    </div>
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
