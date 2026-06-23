<?php

namespace App\Actions\Issues;

use App\Http\Resources\IssueResource;
use App\Http\Resources\TagResource;
use App\Models\Issue;
use App\Models\Tag;
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
            'allTags' => TagResource::collection(Tag::query()->orderBy('name')->get())->resolve($request),
            'comments' => $comments,
            'issue' => IssueResource::make($issue)->resolve($request),
        ];
    }
}
