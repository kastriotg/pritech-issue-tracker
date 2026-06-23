<?php

namespace App\Actions\Issues;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateIssueAction
{
    /**
     * @param  array{
     *     project_id: int|string,
     *     title: string,
     *     description?: string|null,
     *     status: string,
     *     priority: string,
     *     due_date?: string|null,
     *     tag_ids?: list<int|string>
     * }  $attributes
     */
    public function handle(User $user, array $attributes): Issue
    {
        return DB::transaction(function () use ($user, $attributes): Issue {
            $tagIds = $attributes['tag_ids'] ?? [];
            $projectId = $attributes['project_id'];

            unset($attributes['tag_ids'], $attributes['project_id']);

            $project = Project::query()
                ->whereBelongsTo($user)
                ->findOrFail($projectId);

            $issue = $project->issues()->create($attributes);
            $issue->tags()->sync($tagIds);
            $issue->setRelation('project', $project);

            return $issue;
        });
    }
}
