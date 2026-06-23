<?php

namespace App\Actions\Issues;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class PrepareIssueFormAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(Request $request, User $user, ?Issue $issue = null): array
    {
        $selectedProject = null;
        $projects = collect();

        if ($issue) {
            $projects = Project::query()
                ->visibleTo($user)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        } elseif ($request->filled('project_id')) {
            $selectedProject = Project::query()
                ->visibleTo($user)
                ->select(['id', 'user_id', 'name'])
                ->findOrFail($request->integer('project_id'));
        } else {
            $projects = Project::query()
                ->visibleTo($user)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        }

        return [
            'issue' => $issue,
            'projects' => $projects,
            'selectedProject' => $selectedProject,
            'tags' => Tag::query()->orderBy('name')->get(),
        ];
    }
}
