<?php

use App\Http\Requests\StoreTagRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

it('authorizes tag store requests', function () {
    expect((new StoreTagRequest)->authorize())->toBeTrue();
});

it('validates tag request data', function (callable $data, bool $passes) {
    $validator = Validator::make($data(), (new StoreTagRequest)->rules());

    expect($validator->passes())->toBe($passes);
})->with([
    'valid tag' => [
        fn () => ['name' => 'Bug', 'color' => '#EF4444'],
        true,
    ],
    'valid without color' => [
        fn () => ['name' => 'Feature'],
        true,
    ],
    'missing name' => [
        fn () => ['color' => '#EF4444'],
        false,
    ],
    'duplicate name' => [
        function () {
            Tag::factory()->create(['name' => 'Bug']);

            return ['name' => 'Bug', 'color' => '#EF4444'];
        },
        false,
    ],
    'invalid color' => [
        fn () => ['name' => 'Bug', 'color' => 'red'],
        false,
    ],
]);

it('lists tags with issue counts', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $tag = Tag::factory()->create([
        'name' => 'Backend',
        'color' => '#0EA5E9',
    ]);
    $otherTag = Tag::factory()->create([
        'name' => 'Frontend',
    ]);

    Issue::factory()
        ->count(2)
        ->for($project)
        ->create()
        ->each(fn (Issue $issue) => $issue->tags()->attach($tag));

    $this->actingAs($user)
        ->get(route('tags.index'))
        ->assertSuccessful()
        ->assertSee('Backend')
        ->assertSee('#0EA5E9')
        ->assertSee('2 issues')
        ->assertSee('Frontend')
        ->assertSee('0 issues')
        ->assertSee('Create Tag');
});

it('creates a tag', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('tags.store'), [
            'name' => 'Urgent',
            'color' => '#DC2626',
        ])
        ->assertRedirect(route('tags.index'));

    $this->assertDatabaseHas('tags', [
        'name' => 'Urgent',
        'color' => '#DC2626',
    ]);
});
