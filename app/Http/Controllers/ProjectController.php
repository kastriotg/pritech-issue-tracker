<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a paginated list of projects for the authenticated user.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->whereBelongsTo($request->user())
            ->withCount('issues')
            ->latest()
            ->paginate(10);

        return view('projects.index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new project.
     *
     * @return View
     */
    public function create(): View
    {
        return view('projects.create');
    }

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     * @return RedirectResponse
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Project::query()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return to_route('projects.show', $project)
            ->with('status', 'Project created.');
    }

    /**
     * Display the specified project.
     *
     * @param Request $request
     * @param Project $project
     * @return View
     */
    public function show(Request $request, Project $project): View
    {
        $this->authorizeProjectOwner($request, $project);

        $project->load([
            'issues' => fn ($query) => $query
                ->with('tags')
                ->withCount('comments')
                ->latest(),
        ]);

        return view('projects.show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param Request $request
     * @param Project $project
     * @return View
     */
    public function edit(Request $request, Project $project): View
    {
        $this->authorizeProjectOwner($request, $project);

        return view('projects.edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified project in storage.
     *
     * @param UpdateProjectRequest $request
     * @param Project $project
     * @return RedirectResponse
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorizeProjectOwner($request, $project);

        $project->update($request->validated());

        return to_route('projects.show', $project)
            ->with('status', 'Project updated.');
    }

    /**
     * Remove the specified project from storage.
     *
     * @param Request $request
     * @param Project $project
     * @return RedirectResponse
     */
    public function destroy(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProjectOwner($request, $project);

        $project->delete();

        return to_route('projects.index')
            ->with('status', 'Project deleted.');
    }

    /**
     * Authorize that the current user owns the specified project.
     *
     * @param Request $request
     * @param Project $project
     * @return void
     */
    private function authorizeProjectOwner(Request $request, Project $project): void
    {
        abort_unless((int) $project->user_id === $request->user()->id, 404);
    }
}
