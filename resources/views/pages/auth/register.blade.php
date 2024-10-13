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
                <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0 text-white">
                    <div class="flex justify-center">
                        <img class="mb-5 w-56" src="assets/images/logo.png" alt="Portfoline">
                    </div>
                    <div class="w-full sm:w-full md:w-96 bg-slate-800 rounded-lg shadow border-2 border-teal-400 md:mt-0 sm:max-w-md xl:p-0">
                            <div class="px-4 py-6 space-y-4 md:space-y-6">
                            <!--<h1 class="text-xl font-bold leading-tight tracking-tight md:text-2xl ">
                                Entrar
                            </h1>-->
                            <form class="space-y-3 md:space-y-4" action="{{route('login.store')}}" method="POST">
                                @csrf
                                <div>
                                    <label for="email" class="block mb-2 text-sm font-medium ">Email</label>
                                    <input type="email" name="email" id="email" class="bg-slate-600 border border-slate-300 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-teal-600 block w-full p-3" placeholder="name@company.com" required="">
                                </div>
                                <div>
                                    <label for="password" class="block mb-2 text-sm font-medium ">Senha</label>
                                    <input type="password" name="password" id="password" placeholder="••••••••" class="border-slate-300 bg-slate-600 border sm:text-sm rounded-lg focus:ring-primary-600 focus:border-teal-600 block w-full p-3" required="">
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
                                    <a href="{{ route('login.index') }}" class="block text-center w-full text-teal-400 mt-4">Já tenho conta</a>
                                </div>
                                {{-- <p class="text-sm text-center ont-light text-gray-500 dark:text-gray-400">
                                    Don’t have an account yet? <a href="#" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Sign up</a>
                                </p> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="fixed items-center w-full bg-slate-800 bottom-0 left-0 text-center py-1">
            <p class="m-0 text-sm font-medium">Desenvolvido com 💚 por Jefferson Carvalho</p>
        </div>
    </body>
</html>
