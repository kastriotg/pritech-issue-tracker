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
        ->withMembers()
        ->create();

    $issue->tags()->attach($tags);

    $this->assertModelExists($issue);
    expect($issue->project)->toBeInstanceOf(Project::class)
        ->and($issue->comments)->toHaveCount(2)
        ->and($issue->tags)->toHaveCount(2)
        ->and($issue->members)->toHaveCount(2);
});

it('seeds projects issues tags and comments', function () {
    $this->seed(DatabaseSeeder::class);

    $issueCount = Issue::query()->count();

    $this->assertDatabaseCount('tags', 5);
    $this->assertDatabaseCount('users', 16);
    $this->assertDatabaseCount('projects', 30);
    expect($issueCount)->toBeBetween(30, 150);
    $this->assertDatabaseCount('comments', $issueCount * 2);
    expect(Issue::query()->has('tags', '>=', 2)->count())->toBe($issueCount);
    expect(Issue::query()->has('members', '>=', 2)->count())->toBe($issueCount);
});
