<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::select()->where(['id_user' => Auth::user()->id])->get();
        return view('pages.dashboard.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.dashboard.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Course::create([
                'id_user' => Auth::user()->id,
                'title' => $request->title,
                'institution' => $request->institution,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration' => $request->duration,
                'description' => $request->description
            ]);
            return redirect(route('dashboard.courses'))->with(['success' => 'Curso criada com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.courses'))->with(['error' => 'Ocorreu um erro criar o urso. Reporte os detalhes: ' . $e->getMessage()]);
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
        $course = Course::select()->where([
            'id' => $id,
            'id_user' => Auth::user()->id
        ])->first();
        return view('pages.dashboard.courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            Course::where([
                'id' => $id,
                'id_user' => Auth::user()->id
            ])->update([
                'title' => $request->title,
                'institution' => $request->institution,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration' => $request->duration,
                'description' => $request->description
            ]);

            return redirect(route('dashboard.courses'))->with(['success' => 'Curso alterado com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.courses'))->with(['error' => 'Ocorreu um erro ao atualizar o curso. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Course::where([
                'id' => $id,
                'id_user' => Auth::user()->id
            ])->delete();

            return redirect(route('dashboard.courses'))->with(['success' => 'Curso removido com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.courses'))->with(['error' => 'Ocorreu um erro ao remover o curso. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }
}
