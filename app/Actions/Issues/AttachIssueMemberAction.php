<?php

namespace App\Actions\Issues;

use App\Models\Issue;
use App\Models\User;

class AttachIssueMemberAction
{
    public function handle(Issue $issue, User $member): Issue
    {
        $issue->members()->syncWithoutDetaching([$member->id]);

        return $issue->load('members:id,name,email');
    }
}
