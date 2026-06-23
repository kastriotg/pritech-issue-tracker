<?php

namespace App\Actions\Issues;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateIssueAction
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
    public function handle(User $user, Issue $issue, array $attributes): Issue
    {
        return DB::transaction(function () use ($user, $issue, $attributes): Issue {
            $tagIds = $attributes['tag_ids'] ?? [];
            $projectId = $attributes['project_id'];

            unset($attributes['tag_ids']);

            Project::query()
                ->whereBelongsTo($user)
                ->findOrFail($projectId);

            $issue->update($attributes);
            $issue->tags()->sync($tagIds);

            return $issue->refresh();
        });
    }
}
