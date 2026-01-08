<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Workspace;
use App\Models\Topic;
use App\Models\RecordFieldValue;
use App\Models\Collaborator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        /**
         * ----------------------------------------------
         *  WORKSPACES + CONTAGEM
         * ----------------------------------------------
         */
        $workspaces = $user->workspaces()
            ->withCount([
                'topics',
                'topics as fields_count' => function ($query) {
                    $query->leftJoin('topic_records', 'topic_records.topic_id', '=', 'topics.id')
                        ->leftJoin('record_field_values', 'record_field_values.record_id', '=', 'topic_records.id');
                }
            ])
            ->groupBy('workspaces.id')
            ->get();


        /**
         * ----------------------------------------------
         *  ESTATÍSTICAS GERAIS
         * ----------------------------------------------
         */
        $workspacesCount = $workspaces->count();
        $topicsCount = $user->topics()->count();

        // conta todos os values existentes
        $fieldsCount = RecordFieldValue::whereHas('record.topic.workspace', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();


        /**
         * ----------------------------------------------
         *  WORKSPACES RECENTES
         * ----------------------------------------------
        */
        $recentWorkspaces = $user->workspaces()
        ->withCount([
            'topics',
                'topics as fields_count' => function ($query) {
                    $query->leftJoin('topic_records', 'topic_records.topic_id', '=', 'topics.id')
                        ->leftJoin('record_field_values', 'record_field_values.record_id', '=', 'topic_records.id');
                }
            ])
            ->groupBy('workspaces.id')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($workspace) {
                return [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'is_published' => $workspace->is_published,
                    'topics_count' => $workspace->topics_count,
                    'fields_count' => $workspace->fields_count ?? 0,
                    'updated_at' => $workspace->updated_at->toISOString(),
                    'created_at' => $workspace->created_at->toISOString(),
                ];
            });
            
            /**
         * ----------------------------------------------
         *  WORKSPACES MAIS ATIVOS
         * ----------------------------------------------
         */
        $mostActiveWorkspaces = $user->workspaces()
            ->withCount([
                'topics',
                'topics as fields_count' => function ($query) {
                    $query->leftJoin('topic_records', 'topic_records.topic_id', '=', 'topics.id')
                        ->leftJoin('record_field_values', 'record_field_values.record_id', '=', 'topic_records.id');
                }
            ])
            ->groupBy('workspaces.id')
            ->orderBy('fields_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($workspace) {
                return [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'fields_count' => $workspace->fields_count ?? 0,
                    'topics_count' => $workspace->topics_count,
                ];
            });


        /**
         * ----------------------------------------------
         *  ESTATÍSTICAS DETALHADAS
         * ----------------------------------------------
        */
        $publishedWorkspaces = $workspaces->where('is_published', true)->count();
        $privateWorkspaces = $workspaces->where('is_published', false)->count();

        // tópicos que possuem registros
        $topicsWithFields = Topic::whereHas('workspace', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->has('records')
        ->count();

        // como a tabela fields não existe mais
        $visibleFields = 0;
        $hiddenFields = 0;


        /**
         * ----------------------------------------------
         *  COLABORAÇÕES
         * ----------------------------------------------
         */
        $collaborationsCount = $user->collaborations()->count();
        $activeCollaborations = $user->collaborations()->where('status', 'accepted')->count();


        /**
         * ----------------------------------------------
         *  LIMITES DO PLANO
         * ----------------------------------------------
         */
        $plan = $user->getPlan();
        $workspaceLimit = $plan->workspaces === 0 ? 999 : $plan->workspaces;
        $fieldsLimit = $plan->fields === 0 ? 999 : $plan->fields;

        $greetingMessage = $this->getGreetingMessage($user);

        return Inertia::render('Dashboard/Home/Home', [
            'lang' => __('about'),
            'workspacesCount' => $workspacesCount,
            'topicsCount' => $topicsCount,
            'fieldsCount' => $fieldsCount,
            'recentWorkspaces' => $recentWorkspaces,
            'mostActiveWorkspaces' => $mostActiveWorkspaces,
            'publishedWorkspaces' => $publishedWorkspaces,
            'privateWorkspaces' => $privateWorkspaces,
            'topicsWithFields' => $topicsWithFields,
            'visibleFields' => $visibleFields,
            'hiddenFields' => $hiddenFields,
            'collaborationsCount' => $collaborationsCount,
            'activeCollaborations' => $activeCollaborations,
            'workspaceLimit' => $workspaceLimit,
            'fieldsLimit' => $fieldsLimit,
            'greetingMessage' => $greetingMessage,
            'planLimits' => [
                'workspaces' => [
                    'current' => $workspacesCount,
                    'limit' => $workspaceLimit,
                    'percentage' => ($workspaceLimit > 0 && $workspaceLimit !== 999) ? ($workspacesCount / $workspaceLimit) * 100 : 0
                ],
                'fields' => [
                    'current' => $fieldsCount,
                    'limit' => $fieldsLimit,
                    'percentage' => ($fieldsLimit > 0 && $fieldsLimit !== 999) ? ($fieldsCount / $fieldsLimit) * 100 : 0,
                ]
            ]
        ]);
    }



    /**
     * Get personalized greeting message based on time of day
     */
    private function getGreetingMessage($user)
    {
        $hour = Carbon::now()->hour;

        if ($hour < 12) {
            $greeting = 'Bom dia';
        } elseif ($hour < 18) {
            $greeting = 'Boa tarde';
        } else {
            $greeting = 'Boa noite';
        }
        
        $messages = [
            "{$greeting}! Pronto para organizar seus dados? 📊",
            "{$greeting}! Seus workspaces estão te esperando 🚀",
            "{$greeting}! Hora de criar algo incrível 💫",
            "{$greeting}! Vamos simplificar seus dados hoje? 🔧",
            // "{$greeting}! Seu hub de dados está atualizado 📈"
        ];
        
        return $messages[array_rand($messages)];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

public function about()
    {
        $user = Auth::user();
        $api_count = $user->workspaces()->withCount('topics')->get()->sum('topics_count');

        return Inertia::render('Dashboard/About/About', ['lang' => __('about')]);
    }
}
