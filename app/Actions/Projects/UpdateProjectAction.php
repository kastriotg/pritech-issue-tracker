<?php

namespace App\Actions\Projects;

use App\Models\Project;

class UpdateProjectAction
{
    /**
     * @param  array{name: string, description?: string|null}  $attributes
     */
    public function handle(Project $project, array $attributes): Project
    {
        $project->update($attributes);

        return $project;
    }
}
