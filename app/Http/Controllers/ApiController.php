<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Topic;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function refresh()
    {
        try {
            // A middleware 'jwt.refresh' já validou que o token pode ser renovado.
            // O método refresh() obtém o novo token.
            $newToken = Auth::guard('api')->refresh();

            return response()->json([
                'status' => 'success',
                'authorization' => [
                    'token' => $newToken,
                    'type' => 'bearer',
                    'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
                ]
            ]);
        } catch (JWTException $e) {
            // Em caso de qualquer erro na renovação (token na blacklist, por exemplo).
            return response()->json(['error' => 'Token inválido ou não pode ser renovado.'], 401);
        }
    }

    //Fornecer as hashes para garantir o tokenJWT
    public function getTokenByHashes(Request $request){
        $auth = auth('api');

        $primaryHash = $request->input('primary_hash_api');
        $secondaryHash = $request->input('secondary_hash_api');

        if (!$primaryHash || !$secondaryHash) {
            return response()->json(['error' => 'Hashes not provided.'], 400);
        }

        //consulta do usuario no banco
        $user = User::where([
            'primary_hash_api' => $primaryHash,
            'secondary_hash_api' => $secondaryHash,
        ])->first();

        //Se o usuário não for encontrado
        if (!$user) {
            return response()->json(['error' => 'Invalid hashes.'], 401);
        }

        try{
            $token = $auth->fromUser($user);
            return response()->json([
                'status' => 'success',
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'expires_in' => $auth->factory()->getTTL() * 60
                ]
            ]);
        }catch(\Exeption $e){
            
        }

    }

    public function getVisibleWorkspaceData(string $workspaceId)
    {
        try {
            $workspace = Workspace::with(['user', 'typeWorkspace', 'topics.fields'])
                ->where('id', $workspaceId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Transformar para formato JSON estruturado
            $data = [
                'metadata' => [
                    'version' => '1.0',
                    'generated_at' => now()->toISOString(),
                    'workspace_id' => $workspace->id
                ],
                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'description' => $workspace->description ?? '',
                    'type' => $workspace->typeWorkspace->description,
                    'type_id' => $workspace->type_workspace_id,
                    'is_published' => $workspace->is_published,
                    'owner' => [
                        'id' => $workspace->user->id,
                        'name' => $workspace->user->name,
                        'email' => $workspace->user->email
                    ],
                    'dates' => [
                        'created' => $workspace->created_at->toISOString(),
                        'updated' => $workspace->updated_at->toISOString()
                    ]
                ],
                'topics' => $workspace->topics->map(function($topic) {
                    return [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'order' => $topic->order,
                        'fields_count' => $topic->fields->count(),
                        'fields' => $topic->fields->map(function($field) {
                            return [
                                'id' => $field->id,
                                'key' => $field->key_name,
                                'value' => $field->value,
                                'visibility' => (bool) $field->is_visible,
                                'order' => $field->order,
                                'metadata' => [
                                    'created' => $field->created_at->toISOString(),
                                    'updated' => $field->updated_at->toISOString()
                                ]
                            ];
                        })
                    ];
                }),
                'statistics' => [
                    'total_topics' => $workspace->topics->count(),
                    'total_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->count();
                    }),
                    'visible_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->where('is_visible', true)->count();
                    })
                ]
            ];

            return response()->json($data, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Workspace não encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }  

    public function getWorkspaceData(string $workspaceId){
        try {
            // Verificar se o workspace existe e pertence ao usuário
            $workspace = Workspace::with(['user', 'typeWorkspace'])
                ->where('id', $workspaceId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Carregar tópicos apenas com campos visíveis
            $topics = Topic::with(['fields' => function($query) {
                    $query->where('is_visible', 1) // ← FILTRO AQUI
                        ->orderBy('order');
                }])
                ->where('workspace_id', $workspaceId)
                ->orderBy('order')
                ->get();

            // Estrutura da resposta
            $response = [
                'workspace' => [
                    'id' => $workspace->id,
                    'title' => $workspace->title,
                    'type' => $workspace->typeWorkspace->description,
                    'is_published' => $workspace->is_published,
                    'created_at' => $workspace->created_at,
                    'updated_at' => $workspace->updated_at
                ],
                'topics' => $topics->map(function($topic) {
                    return [
                        'id' => $topic->id,
                        'title' => $topic->title,
                        'order' => $topic->order,
                        // 'fields' => $topic->fields->map(function($field) {
                        //     return [
                        //         'id' => $field->id,
                        //         'key_name' => $field->key_name,
                        //         'value' => $field->value,
                        //         'order' => $field->order,
                        //         'created_at' => $field->created_at,
                        //         'updated_at' => $field->updated_at
                        //     ];
                        // })
                        'fields' => $topic->fields->mapWithKeys(function($field) {
                            return [$field->key_name => $field->value];
                        })
                    ];
                }),
                'statistics' => [
                    'total_topics' => $workspace->topics->count(),
                    'total_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->count();
                    }),
                    'visible_fields' => $workspace->topics->sum(function($topic) {
                        return $topic->fields->where('is_visible', true)->count();
                    })
                ]
            ];

            return response()->json($response, 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Workspace não encontrado ou você não tem permissão'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar dados: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCourseById(Request $request)
    {
        // try {
        //     $course = Course::where([
        //         'id_user' => Auth::id(),
        //         'id' => $request->idCourse
        //     ])->first();

        //     if(!$course){
        //         return response()->json(['message' => 'Projeto não encontrado'], 404);
        //     }

        //     return response()->json($course);
        // } catch (\Exception $e) {
        //     //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
        //     return response()->json(['message' => 'An error occurred. ' . $e->getMessage()], 500);
        // }
    }

    public function getProjectById(Request $request)
    {
        // try {
        //     $project = Project::select(
        //         'id',
        //         'title',
        //         'subtitle',
        //         'description',
        //         DB::raw('JSON_UNQUOTE(JSON_EXTRACT(images, "$[0]")) as cover'),
        //         'images',
        //         'start_date',
        //         'end_date',
        //         'status',
        //         'technologies_used',
        //         'project_link',
        //         'git_repository_link'
        //     )->where([
        //         'id_user' => Auth::id(), 
        //         'id' => $request->idProject
        //     ])->first();

        //     if(!$project){
        //         return response()->json(['message' => 'Project not found.'], 404);
        //     }

        //     return response()->json($project);
        // } catch (\Exception $e) {
        //     //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
        //     return response()->json(['message' => 'An error occurred. ' . $e->getMessage()], 500);
        // }
    }
}
