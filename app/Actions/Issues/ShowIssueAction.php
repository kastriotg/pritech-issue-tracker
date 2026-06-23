<?php

namespace App\Actions\Issues;

use App\Http\Resources\IssueResource;
use App\Models\Issue;
use Illuminate\Http\Request;

class ShowIssueAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(Request $request, Issue $issue): array
    {
        $issue->loadMissing(['project:id,user_id,name', 'tags:id,name,color']);
        $comments = $issue->comments()->latest()->paginate(10);
        $issue->setAttribute('comments_count', $comments->total());

        return [
            'comments' => $comments,
            'issue' => IssueResource::make($issue)->resolve($request),
        ];
    }
}
