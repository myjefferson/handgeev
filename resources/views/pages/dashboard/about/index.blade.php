@extends('layout.template')

@section('content_dashboard')
    <div class="w-full flex items-center justify-center mt-7 gap-4">
        <div class="space-y-5">
            <div class="text-center text-2xl">
                Sobre o Portfoline
            </div>
            <div class="text-center">
                Portfoline é uma solução inovadora que simplifica a criação de  portfólios pessoais automatizados, transformando dados curriculares em  sites profissionais com mínimo esforço.
            </div>
            <div class="text-center">
                Versão atual: {{env("APP_VERSION")}}
            </div>
            <div class="text-center">
                Desenvolvido por Jefferson Carvalho
            </div>
            <div class="text-center">
                Quer ajudar a melhorar o Portfoline? Sua contribuição é bem-vinda!
            </div>
        </div>
    </div>
@endsection
