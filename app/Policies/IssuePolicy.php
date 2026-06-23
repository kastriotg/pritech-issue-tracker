<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IssuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Issue $issue): bool|Response
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Issue $issue): bool|Response
    {
        return $this->ownsIssueProject($user, $issue) ?: Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Issue $issue): bool|Response
    {
        return $this->ownsIssueProject($user, $issue) ?: Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Issue $issue): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Issue $issue): bool
    {
        return false;
    }

    private function ownsIssueProject(User $user, Issue $issue): bool
    {
        if (! $issue->relationLoaded('project')) {
            $issue->load('project:id,user_id');
        }

        return (int) $issue->project->user_id === $user->id;
    }
}
