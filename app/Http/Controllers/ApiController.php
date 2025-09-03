<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Experience;
use App\Models\Project;
use App\Models\User;
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

    public function getPersonalData(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'User not authenticated or found.'], 401);
            }
            
            return response()->json($user);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An internal server error occurred.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getExperiences(Request $request) {
        try {
            $userId = Auth::id();

            if(!$userId){
                return response()->json(['error', 'User not authenticated.'], 401);
            }

            $experiences = Experience::where([
                'id_user' => $userId
            ])->get();

            return response()->json($experiences);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An internal server error occurred.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCourses(Request $request)
    {
        try {
            $userId = Auth::id();

            if(!$userId){
                return response()->json(['error', 'User not authenticated.'], 401);
            }

            $courses = Course::where(['id_user' => $this->getUserId($request)])->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    public function getCourseById(Request $request)
    {
        try {
            $course = Course::where([
                'id_user' => Auth::id(),
                'id' => $request->idCourse
            ])->first();

            if(!$course){
                return response()->json(['message' => 'Projeto não encontrado'], 404);
            }

            return response()->json($course);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            return response()->json(['message' => 'An error occurred. ' . $e->getMessage()], 500);
        }
    }

    public function getProjects(Request $request)
    {
        try {
            $userId = Auth::id();

            if(!$userId){
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $courses = Project::select(
                'id',
                'title',
                'subtitle',
                'description',
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(images, "$[0]")) as cover'),
                'images',
                'start_date',
                'end_date',
                'status',
                'technologies_used',
                'project_link',
                'git_repository_link'
            )->where([
                'id_user' => $userId
            ])->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An internal server error occurred.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProjectById(Request $request)
    {
        try {
            $project = Project::select(
                'id',
                'title',
                'subtitle',
                'description',
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(images, "$[0]")) as cover'),
                'images',
                'start_date',
                'end_date',
                'status',
                'technologies_used',
                'project_link',
                'git_repository_link'
            )->where([
                'id_user' => Auth::id(), 
                'id' => $request->idProject
            ])->first();

            if(!$project){
                return response()->json(['message' => 'Project not found.'], 404);
            }

            return response()->json($project);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            return response()->json(['message' => 'An error occurred. ' . $e->getMessage()], 500);
        }
    }
}
