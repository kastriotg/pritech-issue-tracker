<?php

namespace App\Http\Controllers;

use App\Actions\Issues\CreateIssueAction;
use App\Actions\Issues\DeleteIssueAction;
use App\Actions\Issues\ListIssuesAction;
use App\Actions\Issues\PrepareIssueFormAction;
use App\Actions\Issues\ShowIssueAction;
use App\Actions\Issues\UpdateIssueAction;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ListIssuesAction $listIssues): View
    {
        return view('issues.index', $listIssues->handle($request, $request->user()));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, PrepareIssueFormAction $prepareIssueForm): View
    {
        return view('issues.create', $prepareIssueForm->handle($request, $request->user()));
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
    public function show(Request $request, Issue $issue, ShowIssueAction $showIssue): View
    {
        $issue->load('project:id,user_id,name');
        Gate::authorize('view', $issue);

        return view('issues.show', $showIssue->handle($request, $issue));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Issue $issue, PrepareIssueFormAction $prepareIssueForm): View
    {
        $issue->load(['project:id,user_id,name', 'tags:id,name,color']);
        Gate::authorize('update', $issue);

        return view('issues.edit', $prepareIssueForm->handle($request, $request->user(), $issue));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIssueRequest $request, Issue $issue, UpdateIssueAction $updateIssue): RedirectResponse
    {
        $issue->load('project:id,user_id');
        Gate::authorize('update', $issue);

        $updateIssue->handle($request->user(), $issue, $request->validated());

        return to_route('issues.show', $issue)
            ->with('status', 'Issue updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Issue $issue, DeleteIssueAction $deleteIssue): RedirectResponse
    {
        $issue->load('project:id,user_id');
        Gate::authorize('delete', $issue);

        $project = $issue->project;
        $deleteIssue->handle($issue);

        return to_route('projects.show', $project)
            ->with('status', 'Issue deleted.');
    }
}
