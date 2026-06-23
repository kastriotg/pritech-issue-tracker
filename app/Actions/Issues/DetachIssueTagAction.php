<?php

namespace App\Actions\Issues;

use App\Models\Issue;
use App\Models\Tag;

class DetachIssueTagAction
{
    public function handle(Issue $issue, Tag $tag): Issue
    {
        $issue->tags()->detach($tag->id);

        return $issue->load('tags:id,name,color');
    }
}
