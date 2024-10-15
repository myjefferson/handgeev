<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Portfoline</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite('resources/css/app.css')

        {{-- Flowbite JS --}}
        <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

    </head>
    <body class="font-sans antialiased bg-slate-950 text-white">
        <div>
            <section>
                <div class="w-full grid grid-cols-[auto_400px] items-start mx-auto md:h-screen lg:py-0 text-white">
                    <div class="p-5 h-full flex flex-col justify-between">
                        <div>
                            <img class="mb-5 w-56" src="assets/images/logo.png" alt="Portfoline">
                        </div>
                        <div>
                            <div class="text-3xl font-medium">
                                Apresente-se como quiser.
                            </div>
                            <div class="text-2xl font-medium">
                                Mostre porque veio.
                            </div>
                        </div>
                    </div>
                    <div class="w-full h-full relative bg-slate-800 border-l-2 border-teal-400 md:mt-0 sm:max-w-md xl:p-0">
                        <div class="px-4 py-4 h-full flex items-center space-y-4 md:space-y-6">
                            <div>
                                <h1 class="text-xl font-semibold leading-tight tracking-tight md:text-2xl">
                                    Criar conta
                                </h1>
                                <p class="text-sm mt-5 mb-5">
                                    Já tem uma conta? <a href="{{route('login.index')}}" class="underline text-teal-400">Fazer login</a>.
                                <p>
                                <form class="space-y-2 md:space-y-3" action="{{route('register.store')}}" method="POST">
                                    @csrf
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="name" class="block mb-2 text-sm font-medium ">Nome</label>
                                            <input type="text" name="name" id="name" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:border-teal-600 block w-full p-3" placeholder="Ex: Maria" required="">
                                        </div>
                                        <div>
                                            <label for="surname" class="block mb-2 text-sm font-medium ">Sobrenome</label>
                                            <input type="text" name="surname" id="surname" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:border-teal-600 block w-full p-3" placeholder="Ex: Carvalho" required="">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="email" class="block mb-2 text-sm font-medium ">Email</label>
                                        <input type="email" name="email" id="email" class="bg-slate-600  sm:text-sm rounded-lg focus:ring-primary-600 focus:border-teal-600 block w-full p-3" placeholder="Digite seu melhor e-mail" required="">
                                    </div>
                                    <div>
                                        <label for="password" class="block mb-2 text-sm font-medium ">Senha</label>
                                        <input type="password" name="password" id="password" placeholder="••••••••" class="bg-slate-600 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-teal-600 block w-full p-3" required="">
                                    </div>
                                    {{--<div class="flex items-center justify-between">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800" required="">
                                            </div>
                                                <div class="ml-3 text-sm">
                                                <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
                                            </div>
                                        </div>
                                        <a href="#" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">Forgot password?</a>
                                    </div>--}}
                                    <div class="">
                                        <button type="submit" class="w-full text-slate-900 bg-teal-400 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-md px-5 py-3 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800 mt-4">Criar conta</button>
                                        <p class="text-sm mt-4 text-gray-300 text-center">
                                            Ao crair a conta no Portfoline, você concorda com os nossos Termos e Política de Privacidade.
                                        </p>
                                    </div>
                                </form>
                                <div>
                                    <div class="absolute items-center w-full bg-slate-700 bottom-0 left-0 text-center text-gray-400 py-1">
                                        <p class="m-0 text-sm font-medium">Desenvolvido com 💚 por Jefferson Carvalho</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
