<?php

namespace App\Actions\Issues;

use App\Models\Issue;
use App\Models\User;

class DetachIssueMemberAction
{
    public function handle(Issue $issue, User $member): Issue
    {
        $issue->members()->detach($member->id);

        return $issue->load('members:id,name,email');
    }
}
