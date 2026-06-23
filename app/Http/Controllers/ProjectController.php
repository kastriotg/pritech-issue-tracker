<?php

namespace App\Http\Controllers;

use App\Actions\CreateProjectAction;
use App\Actions\DeleteProjectAction;
use App\Actions\UpdateProjectAction;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a paginated list of projects for the authenticated user.
     */
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->whereBelongsTo($request->user())
            ->withCount('issues')
            ->latest()
            ->paginate(10)
            ->through(fn (Project $project): array => ProjectResource::make($project)->resolve($request));

        return view('projects.index', [
            'projects' => $projects,
        ]);
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
    public function show(Request $request, Project $project): View
    {
        $this->authorizeProjectOwner($request, $project);

        $project->load([
            'issues' => fn ($query) => $query
                ->withCount('comments')
                ->latest(),
        ]);

        $issueTags = collect();
        $issueIds = $project->issues->pluck('id');

        if ($issueIds->isNotEmpty()) {
            $issueTags = DB::table('issue_tag')
                ->join('tags', 'tags.id', '=', 'issue_tag.tag_id')
                ->whereIn('issue_tag.issue_id', $issueIds)
                ->select(['issue_tag.issue_id', 'tags.id', 'tags.name', 'tags.color'])
                ->orderBy('tags.name')
                ->get()
                ->unique(fn ($tag) => $tag->issue_id.'-'.$tag->id)
                ->groupBy('issue_id');
        }

        $project->issues->each(function ($issue) use ($issueTags): void {
            $issue->setAttribute('tag_badges', $issueTags->get($issue->id, collect()));
        });

        return view('projects.show', [
            'project' => ProjectResource::make($project)->resolve($request),
        ]);
    }

    /**
     * Show the form for editing the specified project.
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
     */
    public function update(UpdateProjectRequest $request, Project $project, UpdateProjectAction $updateProject): RedirectResponse
    {
        $this->authorizeProjectOwner($request, $project);

        $updateProject->handle($project, $request->validated());

        return to_route('projects.show', $project)
            ->with('status', 'Project updated.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Request $request, Project $project, DeleteProjectAction $deleteProject): RedirectResponse
    {
        $this->authorizeProjectOwner($request, $project);

        $deleteProject->handle($project);

        return to_route('projects.index')
            ->with('status', 'Project deleted.');
    }

    /**
     * Authorize that the current user owns the specified project.
     */
    private function authorizeProjectOwner(Request $request, Project $project): void
    {
        abort_unless((int) $project->user_id === $request->user()->id, 404);
    }
}
