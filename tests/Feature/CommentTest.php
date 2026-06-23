<?php

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

it('authorizes comment store requests', function () {
    expect((new StoreCommentRequest)->authorize())->toBeTrue();
});

it('validates comment request data', function (callable $data, bool $passes) {
    $validator = Validator::make($data(), (new StoreCommentRequest)->rules());

    expect($validator->passes())->toBe($passes);
})->with([
    'valid comment' => [
        fn () => [
            'body' => 'This needs another pass.',
        ],
        true,
    ],
    'missing body' => [
        fn () => [],
        false,
    ],
]);

it('loads paginated issue comments newest first', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $issue = Issue::factory()->for($project)->create();

    Comment::factory()->count(11)->for($issue)->sequence(
        fn ($sequence): array => [
            'author_name' => "Author {$sequence->index}",
            'body' => "Comment {$sequence->index}",
            'created_at' => Carbon::parse('2026-06-23 14:35:00')->subMinutes(11 - $sequence->index),
        ],
    )->create();

    $this->actingAs($user)
        ->getJson(route('issues.comments.index', $issue))
        ->assertSuccessful()
        ->assertJsonPath('data.0.author_name', 'Author 10')
        ->assertJsonPath('data.0.body', 'Comment 10')
        ->assertJsonPath('data.0.created_at_label', 'Jun 23, 2026 2:34 PM')
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.total', 11);
});

it('stores a comment for an owned issue through ajax', function () {
    $user = User::factory()->create([
        'name' => 'Grace Hopper',
    ]);
    $project = Project::factory()->for($user)->create();
    $issue = Issue::factory()->for($project)->create();

    $this->actingAs($user)
        ->postJson(route('issues.comments.store', $issue), [
            'body' => 'This also happens on mobile.',
        ])
        ->assertCreated()
        ->assertJsonPath('data.issue_id', $issue->id)
        ->assertJsonPath('data.author_name', 'Grace Hopper')
        ->assertJsonPath('data.body', 'This also happens on mobile.');

    $comment = Comment::query()->whereBelongsTo($issue)->firstOrFail();

    expect($comment)
        ->author_name->toBe('Grace Hopper')
        ->body->toBe('This also happens on mobile.');
});

it('returns validation errors when storing a comment through ajax', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $issue = Issue::factory()->for($project)->create();

    $response = $this->actingAs($user)
        ->postJson(route('issues.comments.store', $issue), [
            'body' => '',
        ]);

    expect($response->status())
        ->toBe(422)
        ->and($response->json('errors.body.0'))
        ->toBe('The body field is required.');
});

it('does not expose comments for another users issue', function () {
    $user = User::factory()->create();
    $issue = Issue::factory()->create();

    $this->actingAs($user)
        ->getJson(route('issues.comments.index', $issue))
        ->assertNotFound();
});

it('does not store a comment for another users issue', function () {
    $user = User::factory()->create();
    $issue = Issue::factory()->create();

    $this->actingAs($user)
        ->postJson(route('issues.comments.store', $issue), [
            'body' => 'This also happens on mobile.',
        ])
        ->assertNotFound();

    expect($issue->comments()->count())->toBe(0);
});
