<?php

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

it('authorizes project store and update requests', function () {
    expect((new StoreProjectRequest)->authorize())->toBeTrue()
        ->and((new UpdateProjectRequest)->authorize())->toBeTrue();
});

it('validates project request data', function (array $data, bool $passes) {
    $validator = Validator::make($data, (new StoreProjectRequest)->rules());

    expect($validator->passes())->toBe($passes);
})->with([
    'valid project' => [
        ['name' => 'Website Redesign', 'description' => 'Refresh the customer portal.'],
        true,
    ],
    'valid without description' => [
        ['name' => 'Website Redesign'],
        true,
    ],
    'missing name' => [
        ['description' => 'Refresh the customer portal.'],
        false,
    ],
    'name too long' => [
        ['name' => str_repeat('a', 256), 'description' => 'Refresh the customer portal.'],
        false,
    ],
    'description must be a string' => [
        ['name' => 'Website Redesign', 'description' => ['Refresh the customer portal.']],
        false,
    ],
]);

it('uses the same validation rules for project updates', function () {
    expect((new UpdateProjectRequest)->rules())->toBe((new StoreProjectRequest)->rules());
});

it('shows the authenticated users projects', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create([
        'name' => 'Customer Portal',
        'description' => 'Improve the account dashboard.',
    ]);
    $otherProject = Project::factory()->create([
        'name' => 'Hidden Roadmap',
    ]);

    Issue::factory()->count(2)->for($project)->create();

    $this->actingAs($user)
        ->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('Customer Portal')
        ->assertSee('Improve the account dashboard.')
        ->assertSee('2 issues')
        ->assertDontSee('Hidden Roadmap')
        ->assertSee(route('projects.show', $project, absolute: false))
        ->assertDontSee(route('projects.show', $otherProject, absolute: false));
});

it('shows a project with its issues', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create([
        'name' => 'Mobile App',
        'description' => 'Ship the first mobile release.',
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

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSee('Mobile App')
        ->assertSee('Ship the first mobile release.')
        ->assertSee('Fix login redirect')
        ->assertSee('Users should land on their dashboard.')
        ->assertSee('In Progress')
        ->assertSee('High')
        ->assertSee('Bug')
        ->assertSee('#ff2525', false)
        ->assertSee(route('issues.show', $issue, absolute: false));
});

it('does not show another users project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.show', $project))
        ->assertNotFound();
});

it('creates a project for the authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('projects.store'), [
            'name' => 'API Rewrite',
            'description' => 'Modernize the integration layer.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'API Rewrite',
        'description' => 'Modernize the integration layer.',
    ]);
});

it('updates an owned project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create([
        'name' => 'Old Name',
    ]);

    $this->actingAs($user)
        ->patch(route('projects.update', $project), [
            'name' => 'New Name',
            'description' => 'Updated details.',
        ])
        ->assertRedirect(route('projects.show', $project));

    expect($project->refresh())
        ->name->toBe('New Name')
        ->description->toBe('Updated details.');
});

it('deletes an owned project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('projects.destroy', $project))
        ->assertRedirect(route('projects.index'));

    $this->assertModelMissing($project);
});
