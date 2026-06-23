<?php

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Database\Seeders\DatabaseSeeder;

it('creates a complete issue graph with factories', function () {
    $tags = Tag::factory()->count(2)->create();
    $issue = Issue::factory()
        ->has(Comment::factory()->count(2))
        ->create();

    $issue->tags()->attach($tags);

    $this->assertModelExists($issue);
    expect($issue->project)->toBeInstanceOf(Project::class)
        ->and($issue->comments)->toHaveCount(2)
        ->and($issue->tags)->toHaveCount(2);
});

it('seeds projects issues tags and comments', function () {
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('tags', 5);
    $this->assertDatabaseCount('projects', 5);
    $this->assertDatabaseCount('issues', 15);
    $this->assertDatabaseCount('comments', 30);
    expect(Issue::query()->has('tags', '>=', 2)->count())->toBe(15);
});
