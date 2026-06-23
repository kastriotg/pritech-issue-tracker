<?php

namespace App\Actions\Issues;

use App\Models\Issue;

class DeleteIssueAction
{
    public function handle(Issue $issue): void
    {
        $issue->delete();
    }
}
