<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id_user = Auth::user()->id;
        $personal_data = User::where(['id' => $id_user])->first();
        return view('pages.dashboard.personal_data.index', compact(['personal_data']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        $personal_data = User::where(['id' => $id])->first();
        return view('pages.dashboard.personal_data.edit', compact(['personal_data']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            User::findOrFail($id)->update([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
            ]);

            return redirect(route('dashboard.home'))->with(['success' => 'Conta criada com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.home'))->with(['error' => 'Ocorreu um erro ao criar a conta. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
