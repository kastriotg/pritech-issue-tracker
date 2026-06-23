<?php

namespace App\Actions;

use App\Models\Project;
use App\Models\User;

class CreateProjectAction
{
    /**
     * @param  array{name: string, description?: string|null}  $attributes
     */
    public function handle(User $user, array $attributes): Project
    {
        return Project::query()->create([
            ...$attributes,
            'user_id' => $user->id,
        ]);
    }
}
