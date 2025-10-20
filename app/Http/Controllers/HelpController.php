<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index()
    {
        $popularArticles = [
            [
                'id' => 'create-workspace',
                'title' => 'Como criar seu primeiro workspace',
                'excerpt' => 'Aprenda a configurar seu primeiro workspace e começar a organizar seus dados',
                'category' => 'getting-started',
                'content' => 'Conteúdo completo...',
                'tags' => ['workspace', 'iniciante', 'configuração'],
                'read_time' => 3,
                'views' => 1245
            ],
            [
                'id' => 'field-types',
                'title' => 'Tipos de campos disponíveis',
                'excerpt' => 'Conheça todos os tipos de campos e quando usar cada um',
                'category' => 'fields',
                'content' => 'Conteúdo completo...',
                'tags' => ['campos', 'tipos', 'dados'],
                'read_time' => 5,
                'views' => 892
            ],
            [
                'id' => 'api-keys',
                'title' => 'Gerando e gerenciando chaves da API',
                'excerpt' => 'Guia completo sobre autenticação e uso da API HandGeev',
                'category' => 'api',
                'content' => 'Conteúdo completo...',
                'tags' => ['api', 'autenticação', 'chaves'],
                'read_time' => 7,
                'views' => 567
            ],
            [
                'id' => 'collaboration',
                'title' => 'Trabalhando com colaboradores',
                'excerpt' => 'Como adicionar e gerenciar colaboradores nos seus workspaces',
                'category' => 'workspaces',
                'content' => 'Conteúdo completo...',
                'tags' => ['colaboração', 'workspace', 'time'],
                'read_time' => 4,
                'views' => 723
            ]
        ];

        $faqs = [
            [
                'id' => 1,
                'question' => 'Como faço para atualizar meu plano?',
                'answer' => 'Você pode atualizar seu plano a qualquer momento nas configurações da sua conta...'
            ],
            [
                'id' => 2,
                'question' => 'Existe limite de campos por tópico?',
                'answer' => 'Sim, o limite varia de acordo com seu plano. Consulte a página de planos para detalhes...'
            ],
            [
                'id' => 3,
                'question' => 'Posso exportar meus dados?',
                'answer' => 'Sim, todos os planos permitem exportação de dados em formato JSON...'
            ]
        ];

        $systemStatus = [
            'status' => 'Todos os sistemas operando normalmente',
            'color' => 'green'
        ];

        return view('support.help-center', compact('popularArticles', 'faqs', 'systemStatus'));
    }
}