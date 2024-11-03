<?php

namespace App\Http\Controllers;

use App\Models\Experiences;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function getPersonalData(string $userId/*, string $hash*/)
    {
        try {
            $personal_data = User::where(['id' => $userId])->first();
            return response()->json($personal_data);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    public function getExperiences(string $userId/*, string $hash*/)
    {
        try {
            $experiences = Experiences::where(['id_user' => $userId])->get();
            return response()->json($experiences);
        } catch (\Exception $e) {
            //Retornar pra página de hash inválido, ou configuração de ambiente incompleta
            //return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }
}
