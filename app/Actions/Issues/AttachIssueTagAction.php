<?php

namespace App\Actions\Issues;

use App\Models\Issue;
use App\Models\Tag;

class AttachIssueTagAction
{
    public function handle(Issue $issue, Tag $tag): Issue
    {
        $issue->tags()->syncWithoutDetaching([$tag->id]);

        return $issue->load('tags:id,name,color');
    }
}
