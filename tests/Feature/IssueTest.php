<?php

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

it('authorizes issue store and update requests', function () {
    expect((new StoreIssueRequest)->authorize())->toBeTrue()
        ->and((new UpdateIssueRequest)->authorize())->toBeTrue();
});

it('validates issue request data', function (callable $data, bool $passes) {
    $validator = Validator::make($data(), (new StoreIssueRequest)->rules());

    expect($validator->passes())->toBe($passes);
})->with([
    'valid issue' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'description' => 'Users should land on the dashboard.',
            'status' => 'open',
            'priority' => 'high',
            'due_date' => now()->addWeek()->toDateString(),
            'tag_ids' => Tag::factory()->count(2)->create()->pluck('id')->all(),
        ],
        true,
    ],
    'valid without optional fields' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'status' => 'in_progress',
            'priority' => 'medium',
        ],
        true,
    ],
    'missing project' => [
        fn () => [
            'title' => 'Fix login redirect',
            'status' => 'open',
            'priority' => 'high',
        ],
        false,
    ],
    'invalid status' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'status' => 'blocked',
            'priority' => 'high',
        ],
        false,
    ],
    'invalid priority' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'status' => 'open',
            'priority' => 'urgent',
        ],
        false,
    ],
    'invalid tag id' => [
        fn () => [
            'project_id' => Project::factory()->create()->id,
            'title' => 'Fix login redirect',
            'status' => 'open',
            'priority' => 'high',
            'tag_ids' => [999],
        ],
        false,
    ],
]);

it('uses the same validation rules for issue updates', function () {
    expect((new UpdateIssueRequest)->rules())->toEqual((new StoreIssueRequest)->rules());
});

it('shows the issue creation form for an owned project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create([
        'name' => 'Customer Portal',
    ]);
    $tag = Tag::factory()->create([
        'name' => 'Bug',
        'color' => '#ff2525',
    ]);

    $this->actingAs($user)
        ->get(route('issues.create', ['project_id' => $project->id]))
        ->assertSuccessful()
        ->assertSee('New Issue')
        ->assertSee('Customer Portal')
        ->assertSee('Bug')
        ->assertSee('#ff2525', false)
        ->assertSee('name="project_id"', false)
        ->assertSee((string) $project->id);
});

it('does not show the issue creation form for another users project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('issues.create', ['project_id' => $project->id]))
        ->assertNotFound();
});

it('creates an issue from an owned project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $tags = Tag::factory()->count(2)->create();

    $this->actingAs($user)
        ->post(route('issues.store'), [
            'project_id' => $project->id,
            'title' => 'Fix login redirect',
            'description' => 'Users should land on their dashboard.',
            'status' => 'open',
            'priority' => 'high',
            'due_date' => '2026-07-01',
            'tag_ids' => $tags->pluck('id')->all(),
        ])
        ->assertRedirect(route('projects.show', $project));

    $issue = Issue::query()->where('title', 'Fix login redirect')->firstOrFail();

    expect($issue)
        ->project_id->toBe($project->id)
        ->description->toBe('Users should land on their dashboard.')
        ->status->toBe('open')
        ->priority->toBe('high')
        ->and($issue->tags()->pluck('tags.id')->all())->toEqualCanonicalizing($tags->pluck('id')->all());
});

it('does not create an issue for another users project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->post(route('issues.store'), [
            'project_id' => $project->id,
            'title' => 'Fix login redirect',
            'status' => 'open',
            'priority' => 'high',
        ])
        ->assertNotFound();

    $this->assertDatabaseMissing('issues', [
        'project_id' => $project->id,
        'title' => 'Fix login redirect',
    ]);
});

it('shows filtered issues for the authenticated users projects', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create([
        'name' => 'Customer Portal',
    ]);
    $bug = Tag::factory()->create([
        'name' => 'Bug',
        'color' => '#ff2525',
    ]);
    $matchingIssue = Issue::factory()->for($project)->create([
        'title' => 'Fix checkout total',
        'status' => 'open',
        'priority' => 'high',
    ]);
    $hiddenPriorityIssue = Issue::factory()->for($project)->create([
        'title' => 'Polish dashboard cards',
        'status' => 'open',
        'priority' => 'low',
    ]);
    $otherProjectIssue = Issue::factory()->create([
        'title' => 'Private roadmap item',
        'status' => 'open',
        'priority' => 'high',
    ]);

    $matchingIssue->tags()->attach($bug);

    $this->actingAs($user)
        ->get(route('issues.index', [
            'status' => 'open',
            'priority' => 'high',
            'tag' => $bug->id,
        ]))
        ->assertSuccessful()
        ->assertSee('Fix checkout total')
        ->assertSee('Customer Portal')
        ->assertSee('Bug')
        ->assertSee('#ff2525', false)
        ->assertDontSee('Polish dashboard cards')
        ->assertDontSee('Private roadmap item')
        ->assertDontSee(route('issues.show', $hiddenPriorityIssue, absolute: false))
        ->assertDontSee(route('issues.show', $otherProjectIssue, absolute: false));
});

it('shows an owned issue with tags and comments', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create([
        'name' => 'Mobile App',
    ]);
    $issue = Issue::factory()->for($project)->create([
        'title' => 'Fix login redirect',
        'description' => 'Users should land on their dashboard.',
        'status' => 'in_progress',
        'priority' => 'high',
    ]);
    $tag = Tag::factory()->create([
        'name' => 'Bug',
        'color' => '#ff2525',
    ]);

    $issue->tags()->attach($tag);
    Comment::factory()->for($issue)->create([
        'author_name' => 'Ada Lovelace',
        'body' => 'This also happens on mobile.',
    ]);

    $this->actingAs($user)
        ->get(route('issues.show', $issue))
        ->assertSuccessful()
        ->assertSee('Fix login redirect')
        ->assertSee('Users should land on their dashboard.')
        ->assertSee('Mobile App')
        ->assertSee('In Progress')
        ->assertSee('High')
        ->assertSee('Bug')
        ->assertSee('Ada Lovelace')
        ->assertSee('This also happens on mobile.');
});

it('does not show another users issue', function () {
    $user = User::factory()->create();
    $issue = Issue::factory()->create();

    $this->actingAs($user)
        ->get(route('issues.show', $issue))
        ->assertNotFound();
});

it('shows the issue edit form for an owned issue', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create([
        'name' => 'Customer Portal',
    ]);
    $issue = Issue::factory()->for($project)->create([
        'title' => 'Fix login redirect',
        'status' => 'open',
        'priority' => 'medium',
    ]);
    $tag = Tag::factory()->create([
        'name' => 'Bug',
    ]);

    $issue->tags()->attach($tag);

    $this->actingAs($user)
        ->get(route('issues.edit', $issue))
        ->assertSuccessful()
        ->assertSee('Edit Issue')
        ->assertSee('Fix login redirect')
        ->assertSee('Customer Portal')
        ->assertSee('Bug')
        ->assertSee('checked', false);
});

it('updates an owned issue through the update action', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $issue = Issue::factory()->for($project)->create([
        'title' => 'Old title',
    ]);
    $tag = Tag::factory()->create();

    $this->actingAs($user)
        ->patch(route('issues.update', $issue), [
            'project_id' => $project->id,
            'title' => 'Updated title',
            'description' => 'Updated issue details.',
            'status' => 'closed',
            'priority' => 'low',
            'due_date' => '2026-07-01',
            'tag_ids' => [$tag->id],
        ])
        ->assertRedirect(route('issues.show', $issue));

    expect($issue->refresh())
        ->title->toBe('Updated title')
        ->description->toBe('Updated issue details.')
        ->status->toBe('closed')
        ->priority->toBe('low')
        ->due_date->toDateString()->toBe('2026-07-01')
        ->and($issue->tags()->pluck('tags.id')->all())->toBe([$tag->id]);
});

it('does not move an issue to another users project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $otherProject = Project::factory()->create();
    $issue = Issue::factory()->for($project)->create([
        'title' => 'Original title',
    ]);

    $this->actingAs($user)
        ->patch(route('issues.update', $issue), [
            'project_id' => $otherProject->id,
            'title' => 'Updated title',
            'status' => 'closed',
            'priority' => 'low',
        ])
        ->assertNotFound();

    expect($issue->refresh())
        ->project_id->toBe($project->id)
        ->title->toBe('Original title');
});

it('deletes an owned issue through the delete action', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $issue = Issue::factory()->for($project)->create();

    $this->actingAs($user)
        ->delete(route('issues.destroy', $issue))
        ->assertRedirect(route('projects.show', $project));

    $this->assertModelMissing($issue);
});
