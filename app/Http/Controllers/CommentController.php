<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Display a paginated listing of issue comments.
     */
    public function index(Issue $issue): AnonymousResourceCollection
    {
        $issue->load('project:id,user_id');
        Gate::authorize('view', $issue);

        return CommentResource::collection(
            $issue->comments()
                ->latest()
                ->paginate(5),
        );
    }

    /**
     * Store a newly created issue comment.
     */
    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        $issue->load('project:id,user_id');
        Gate::authorize('view', $issue);

        $comment = $issue->comments()->create([
            ...$request->validated(),
            'author_name' => $request->user()->name,
        ]);

        return CommentResource::make($comment->refresh())
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
