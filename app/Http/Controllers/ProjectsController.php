<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id_user = Auth::user()->id;
        $projects = Project::where(['id_user' => $id_user])->get();
        return view('pages.dashboard.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.dashboard.projects.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Project::create([
                'id_user' => Auth::user()->id,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'technologies_used' => $request->technologies_used,
                'project_link' => $request->project_link,
                'git_repository_link' => $request->git_repository_link
            ]);
            return redirect(route('dashboard.projects'))->with(['success' => 'Projeto adicionado com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.projects'))->with(['error' => 'Ocorreu um erro ao adiciona o projeto. Reporte os detalhes: ' . $e->getMessage()]);
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
        $project = Project::where([
            'id_user' => Auth::user()->id,
            'id' => $id
        ])->first();
        return view('pages.dashboard.projects.form', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            Project::where([
                'id' => $id,
                'id_user' => Auth::user()->id
            ])->update([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'technologies_used' => $request->technologies_used,
                'project_link' => $request->project_link,
                'git_repository_link' => $request->git_repository_link
            ]);
            return redirect(route('dashboard.projects'))->with(['success' => 'Projeto atualizado com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.projects'))->with(['error' => 'Ocorreu um erro ao atualizar o projeto. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Project::where([
                'id' => $id,
                'id_user' => Auth::user()->id
            ])->delete();
            return redirect(route('dashboard.projects'))->with(['success' => 'Projeto removido com sucesso!']);
        } catch (\Exception $e) {
            return redirect(route('dashboard.projects'))->with(['error' => 'Ocorreu um erro ao remover o projeto. Reporte os detalhes: ' . $e->getMessage()]);
        }
    }
}
