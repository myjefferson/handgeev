<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperiencesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $experiences = Experience::where(['id_user' => Auth::user()->id])->get();
        return view('pages.dashboard.experiences.index', compact('experiences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.dashboard.experiences.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Experience::create([
            'id_user' => Auth::user()->id,
            'enterprise' => $request->enterprise,
            'responsibility' => $request->responsibility,
            'description' => $request->description,
            'technologies_used' => $request->technologies_used,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
        try {
            return redirect(route('dashboard.experiences'))->with(['success' => 'Experiência adicionada com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.experiences'))->with(['error' => 'Ocorreu um erro ao adicionar a experiência. Reporte os detalhes: ' . $e->getMessage()]);
        }
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
}
