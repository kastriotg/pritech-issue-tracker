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
        $issue->loadMissing(['project:id,user_id,name', 'tags:id,name,color'])
            ->loadCount('comments');

        return [
            'allTags' => TagResource::collection(Tag::query()->orderBy('name')->get())->resolve($request),
            'issue' => IssueResource::make($issue)->resolve($request),
        ];
    }
}
