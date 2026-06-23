<?php

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
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
