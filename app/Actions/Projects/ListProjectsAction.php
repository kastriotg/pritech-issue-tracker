<?php

namespace App\Actions\Projects;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ListProjectsAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(Request $request, User $user): array
    {
        return [
            'projects' => Project::query()
                ->withCount('issues')
                ->latest()
                ->paginate(10)
                ->through(fn (Project $project): array => ProjectResource::make($project)->resolve($request)),
        ];
    }
}
