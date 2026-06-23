<?php

namespace App\Http\Controllers;

use App\Actions\Issues\AttachIssueTagAction;
use App\Actions\Issues\DetachIssueTagAction;
use App\Http\Resources\TagResource;
use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IssueTagController extends Controller
{
    public function store(
        Request $request,
        Issue $issue,
        Tag $tag,
        AttachIssueTagAction $attachIssueTag,
    ): JsonResponse {
        $issue->load('project:id,user_id');
        Gate::authorize('update', $issue);

        $issue = $attachIssueTag->handle($issue, $tag);

        return $this->tagResponse($request, $issue);
    }

    public function destroy(
        Request $request,
        Issue $issue,
        Tag $tag,
        DetachIssueTagAction $detachIssueTag,
    ): JsonResponse {
        $issue->load('project:id,user_id');
        Gate::authorize('update', $issue);

        $issue = $detachIssueTag->handle($issue, $tag);

        return $this->tagResponse($request, $issue);
    }

    private function tagResponse(Request $request, Issue $issue): JsonResponse
    {
        return response()->json([
            'tags' => TagResource::collection($issue->tags->sortBy('name')->values())->resolve($request),
        ]);
    }
}
