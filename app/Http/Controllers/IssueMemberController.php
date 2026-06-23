<?php

namespace App\Http\Controllers;

use App\Actions\Issues\AttachIssueMemberAction;
use App\Actions\Issues\DetachIssueMemberAction;
use App\Http\Resources\UserResource;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IssueMemberController extends Controller
{
    public function store(
        Request $request,
        Issue $issue,
        User $user,
        AttachIssueMemberAction $attachIssueMember,
    ): JsonResponse {
        $issue->load('project:id,user_id');
        Gate::authorize('update', $issue);

        $issue = $attachIssueMember->handle($issue, $user);

        return $this->memberResponse($request, $issue);
    }

    public function destroy(
        Request $request,
        Issue $issue,
        User $user,
        DetachIssueMemberAction $detachIssueMember,
    ): JsonResponse {
        $issue->load('project:id,user_id');
        Gate::authorize('update', $issue);

        $issue = $detachIssueMember->handle($issue, $user);

        return $this->memberResponse($request, $issue);
    }

    private function memberResponse(Request $request, Issue $issue): JsonResponse
    {
        return response()->json([
            'members' => UserResource::collection($issue->members->sortBy('name')->values())->resolve($request),
        ]);
    }
}
