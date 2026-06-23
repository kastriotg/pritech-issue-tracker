<?php

namespace App\Actions\Projects;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(Request $request, Project $project): array
    {
        $project->load([
            'issues' => fn ($query) => $query
                ->with(['tags:id,name,color'])
                ->withCount('comments')
                ->latest(),
        ]);

        $project->issues->each(function ($issue): void {
            $issue->setAttribute('tag_badges', $issue->tags);
        });

        return [
            'project' => ProjectResource::make($project)->resolve($request),
        ];
    }
}
