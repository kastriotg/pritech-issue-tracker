<?php

namespace App\Actions\Projects;

use App\Models\Project;

class DeleteProjectAction
{
    public function handle(Project $project): void
    {
        $project->delete();
    }
}
