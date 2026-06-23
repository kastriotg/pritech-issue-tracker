<?php

namespace App\Http\Controllers;

use App\Actions\CreateIssueAction;
use App\Http\Requests\StoreIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $selectedProject = null;
        $projects = collect();

        if ($request->filled('project_id')) {
            $selectedProject = Project::query()
                ->whereBelongsTo($request->user())
                ->select(['id', 'user_id', 'name'])
                ->findOrFail($request->integer('project_id'));
        } else {
            $projects = Project::query()
                ->whereBelongsTo($request->user())
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        }

        return view('issues.create', [
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIssueRequest $request, CreateIssueAction $createIssue): RedirectResponse
    {
        $issue = $createIssue->handle($request->user(), $request->validated());

        return to_route('projects.show', $issue->project)
            ->with('status', 'Issue created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Issue $issue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Issue $issue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Issue $issue)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Issue $issue)
    {
        //
    }
}
