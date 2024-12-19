<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Experience;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getUserId($request){
        $user = User::select('id')->where([
            'primary_hash_api' => $request->primary_hash_api,
            'secondary_hash_api' => $request->secondary_hash_api,
        ])->first();
        return $user->id;
    }

    public function getPersonalData(Request $request)
    {
        try {
            $user = User::where(['id' => $this->getUserId($request)])->first();
            return response()->json($user);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    public function getExperiences(Request $request)
    {
        try {
            $experiences = Experience::where(['id_user' => $this->getUserId($request)])->get();
            return response()->json($experiences);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    public function getCourses(Request $request)
    {
        try {
            $courses = Course::where(['id_user' => $this->getUserId($request)])->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    public function getProjects(Request $request)
    {
        try {
            $courses = Project::where(['id_user' => $this->getUserId($request)])->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }
}
