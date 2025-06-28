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
        $user = User::where(['id' => $id_user])->first();
        return view('pages.dashboard.user.index', compact(['user']));
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
    public function edit()
    {
        $user = User::where(['id' => Auth::user()->id])->first();
        return view('pages.dashboard.user.form', compact(['user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            User::findOrFail(Auth::user()->id)->update([
                'email' => $request->email,
                'name' => $request->name,
                'surname' => $request->surname,
                'about_me' => $request->about_me,
                'phone' => $request->phone,
                'nationality' => $request->nationality,
                'marital_status' => $request->marital_status,
                'driver_license_category' => $request->driver_license_category,
                'birthdate' => $request->birthdate,
                'social' => $request->social,
                'postal_code' => $request->postal_code,
                'street' => $request->street,
                'complement' => $request->complement,
                'neighborhood' => $request->neighborhood,
                'city' => $request->city,
                'state' => $request->state,
                'portfolio' => $request->portfolio,
                'personal_site' => $request->personal_site,
            ]);

            return redirect(route('dashboard.personal-data'))->with(['success' => 'Conta criada com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.personal-data'))->with(['error' => 'Ocorreu um erro ao atualizar os dados pessoais. Reporte os detalhes: ' . $e->getMessage()]);
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
