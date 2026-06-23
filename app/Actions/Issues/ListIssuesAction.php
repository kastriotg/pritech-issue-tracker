<?php

namespace App\Actions\Issues;

use App\Http\Resources\IssueResource;
use App\Models\Issue;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class ListIssuesAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(Request $request, User $user): array
    {
        $filters = $request->only(['status', 'priority', 'tag']);

        return [
            'filters' => $filters,
            'issues' => Issue::query()
                ->visibleTo($user)
                ->filtered($filters)
                ->with(['project:id,user_id,name', 'tags:id,name,color'])
                ->withCount('comments')
                ->latest()
                ->paginate(10)
                ->withQueryString()
                ->through(fn (Issue $issue): array => IssueResource::make($issue)->resolve($request)),
            'tags' => Tag::query()->orderBy('name')->get(),
        ];
    }
}
