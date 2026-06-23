<?php

namespace Database\Seeders;

use App\Models\Issue;
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

        Issue::query()->get()->each(function (Issue $issue) use ($tags, $availableTagCount): void {
            if ($availableTagCount === 0) {
                return;
            }

            $issue->tags()->syncWithoutDetaching($tags->random(min(2, $availableTagCount)));
        });
    }
}
