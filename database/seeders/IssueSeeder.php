<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::query()->get();
        $availableTagCount = $tags->count();

        Project::query()->get()->each(function (Project $project) use ($tags, $availableTagCount): void {
            Issue::factory()
                ->count(3)
                ->for($project)
                ->create()
                ->each(function (Issue $issue) use ($tags, $availableTagCount): void {
                    if ($availableTagCount === 0) {
                        return;
                    }

                    $issue->tags()->attach($tags->random(min(2, $availableTagCount)));
                });
        });
    }
}
