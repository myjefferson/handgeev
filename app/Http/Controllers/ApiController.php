<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Experience;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function getPersonalData(string $userId/*, string $hash*/)
    {
        try {
            $user = User::where(['id' => $userId])->first();
            return response()->json($user);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    public function getExperiences(string $userId/*, string $hash*/)
    {
        try {
            $experiences = Experience::where(['id_user' => $userId])->get();
            return response()->json($experiences);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    public function getCourses(string $userId/*, string $hash*/)
    {
        try {
            $courses = Course::where(['id_user' => $userId])->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    public function getProjects(string $userId/*, string $hash*/)
    {
        try {
            $courses = Project::where(['id_user' => $userId])->get();
            return response()->json($courses);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }
}
