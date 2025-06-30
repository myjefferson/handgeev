@extends('layout.template')

@section('content_dashboard')
    <div class="w-full gap-4 p-3">
        <div class="text-2xl text-left mb-10">
            Olá, Jefferson! Como é bom ter você aqui!
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 w-full">

            <section class="perfil bg-slate-700 p-5 rounded-xl m-2">
                <p class="mb-3 text-lg">Perfil</p>
                <div class="flex w-full space-x-3">
                    <div class="bg-slate-600 p-3 rounded-lg w-full">
                        http://localhost:3000/api/profile
                    </div>
                    <button type="button" class="bg-cyan-400 py-0 px-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 23"><g fill="none" stroke="#000000" stroke-linecap="round" stroke-width="2"><path stroke-linejoin="round" d="M15.5 4H18a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2.5"/><path stroke-linejoin="round" d="M8.621 3.515A2 2 0 0 1 10.561 2h2.877a2 2 0 0 1 1.94 1.515L16 6H8z"/><path d="M9 12h6m-6 4h6"/></g></svg>
                    </button>
                </div>
            </section>
            
            <section class="cursos bg-slate-700 p-5 rounded-xl m-2">
                <p class="mb-3 text-lg">Cursos</p>
                <div class="flex w-full space-x-3">
                    <div class="bg-slate-600 p-3 rounded-lg w-full">
                        http://localhost:3000/api/profile
                    </div>
                    <button type="button" class="bg-cyan-400 py-0 px-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 23"><g fill="none" stroke="#000000" stroke-linecap="round" stroke-width="2"><path stroke-linejoin="round" d="M15.5 4H18a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2.5"/><path stroke-linejoin="round" d="M8.621 3.515A2 2 0 0 1 10.561 2h2.877a2 2 0 0 1 1.94 1.515L16 6H8z"/><path d="M9 12h6m-6 4h6"/></g></svg>
                    </button>
                </div>
            </section>
            
            <section class="experiencias-profissionais bg-slate-700 p-5 rounded-xl m-2">
                <p class="mb-3 text-lg">Experiências profissionais</p>
                <div class="flex w-full space-x-3">
                    <div class="bg-slate-600 p-3 rounded-lg w-full">
                        http://localhost:3000/api/profile
                    </div>
                    <button type="button" class="bg-cyan-400 py-0 px-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 23"><g fill="none" stroke="#000000" stroke-linecap="round" stroke-width="2"><path stroke-linejoin="round" d="M15.5 4H18a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2.5"/><path stroke-linejoin="round" d="M8.621 3.515A2 2 0 0 1 10.561 2h2.877a2 2 0 0 1 1.94 1.515L16 6H8z"/><path d="M9 12h6m-6 4h6"/></g></svg>
                    </button>
                </div>
            </section>
            
            <section class="experiencias-profissionais bg-slate-700 p-5 rounded-xl m-2">
                <p class="mb-3 text-lg">Projetos pessoais</p>
                <div class="flex w-full space-x-3">
                    <div class="bg-slate-600 p-3 rounded-lg w-full">
                        http://localhost:3000/api/profile
                    </div>
                    <button type="button" class="bg-cyan-400 py-0 px-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 23"><g fill="none" stroke="#000000" stroke-linecap="round" stroke-width="2"><path stroke-linejoin="round" d="M15.5 4H18a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2.5"/><path stroke-linejoin="round" d="M8.621 3.515A2 2 0 0 1 10.561 2h2.877a2 2 0 0 1 1.94 1.515L16 6H8z"/><path d="M9 12h6m-6 4h6"/></g></svg>
                    </button>
                </div>
            </section>

        </div>
    </div>
@endsection
