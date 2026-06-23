<?php

namespace App\Actions;

use App\Models\Project;

class DeleteProjectAction
{
    public function handle(Project $project): void
    {
        $project->delete();
    }
}
