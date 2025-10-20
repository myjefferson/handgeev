<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Workspace;
use App\Models\Topic;
use App\Models\Field;
use App\Models\Collaborator;
use DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Workspaces do usuário com contagens
        $workspaces = $user->workspaces()->withCount(['topics', 'topics as fields_count' => function($query) {
            $query->select(DB::raw('SUM(
                (SELECT COUNT(*) FROM fields WHERE fields.topic_id = topics.id)
            )'));
        }])->get();
        
        // Estatísticas principais
        $workspacesCount = $workspaces->count();
        $topicsCount = $user->topics()->count();
        
        // Contar campos de forma eficiente
        $fieldsCount = Field::whereHas('topic.workspace', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        
        // Workspaces recentes (últimos 5)
        $recentWorkspaces = $user->workspaces()
            ->withCount(['topics', 'topics as fields_count' => function($query) {
                $query->select(DB::raw('SUM(
                    (SELECT COUNT(*) FROM fields WHERE fields.topic_id = topics.id)
                )'));
            }])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
            
        // Workspaces mais ativos (por número de campos)
        $mostActiveWorkspaces = $user->workspaces()
            ->withCount(['topics', 'topics as fields_count' => function($query) {
                $query->select(DB::raw('SUM(
                    (SELECT COUNT(*) FROM fields WHERE fields.topic_id = topics.id)
                )'));
            }])
            ->orderBy('fields_count', 'desc')
            ->take(5)
            ->get();
            
        // Estatísticas detalhadas
        $publishedWorkspaces = $workspaces->where('is_published', true)->count();
        $privateWorkspaces = $workspaces->where('is_published', false)->count();
        
        $topicsWithFields = Topic::whereHas('workspace', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->has('fields')->count();
        
        $visibleFields = Field::whereHas('topic.workspace', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('is_visible', true)->count();
        
        $hiddenFields = Field::whereHas('topic.workspace', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('is_visible', false)->count();
        
        // Colaborações
        $collaborationsCount = $user->collaborations()->count();
        $activeCollaborations = $user->collaborations()->where('status', 'accepted')->count();
        
        // Limites do plano
        $plan = $user->getPlan();
        $workspaceLimit = $plan->max_workspaces === 0 ? 999 : $plan->max_workspaces;
        $fieldsLimit = $plan->max_fields === 0 ? 999 : $plan->max_fields;
        
        // Mensagem de saudação personalizada
        $greetingMessage = $this->getGreetingMessage($user);

        // Consulta direta nas tabelas do permission
        return view('pages.dashboard.home.index', compact(
            'workspacesCount',
            'topicsCount',
            'fieldsCount',
            'recentWorkspaces',
            'mostActiveWorkspaces',
            'publishedWorkspaces',
            'privateWorkspaces',
            'topicsWithFields',
            'visibleFields',
            'hiddenFields',
            'collaborationsCount',
            'activeCollaborations',
            'workspaceLimit',
            'fieldsLimit',
            'greetingMessage'
        ));
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

    public function about(){
        $api_count = auth()->user()->workspaces()->withCount('topics')->get()->sum('topics_count');
        return view("pages.dashboard.about.index", ['api_count' => $api_count]);
    }

    private function getGreetingMessage($user)
    {
        $hour = now()->hour;
        
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
}
