<?php

namespace App\Http\Controllers;

use App\Actions\Projects\CreateProjectAction;
use App\Actions\Projects\DeleteProjectAction;
use App\Actions\Projects\ListProjectsAction;
use App\Actions\Projects\ShowProjectAction;
use App\Actions\Projects\UpdateProjectAction;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a paginated list of projects for the authenticated user.
     */
    public function index(Request $request, ListProjectsAction $listProjects): View
    {
        return view('projects.index', $listProjects->handle($request, $request->user()));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        return view('projects.create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request, CreateProjectAction $createProject): RedirectResponse
    {
        $project = $createProject->handle($request->user(), $request->validated());

        return to_route('projects.show', $project)
            ->with('status', 'Project created.');
    }

    /**
     * Display the specified project.
     */
    public function show(Request $request, Project $project, ShowProjectAction $showProject): View
    {
        Gate::authorize('view', $project);

        return view('projects.show', $showProject->handle($request, $project));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project, UpdateProjectAction $updateProject): RedirectResponse
    {
        Gate::authorize('update', $project);

        $updateProject->handle($project, $request->validated());

        return to_route('projects.show', $project)
            ->with('status', 'Project updated.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project, DeleteProjectAction $deleteProject): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $deleteProject->handle($project);

        return to_route('projects.index')
            ->with('status', 'Project deleted.');
    }
}
