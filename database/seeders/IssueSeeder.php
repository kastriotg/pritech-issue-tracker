<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Tag;
use App\Models\User;
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
        $users = User::query()->get();
        $availableUserCount = $users->count();

        Issue::query()->get()->each(function (Issue $issue) use ($tags, $availableTagCount, $users, $availableUserCount): void {
            if ($availableTagCount > 0) {
                $issue->tags()->syncWithoutDetaching($tags->random(min(2, $availableTagCount)));
            }

            if ($availableUserCount > 0) {
                $issue->members()->syncWithoutDetaching($users->random(min(2, $availableUserCount)));
            }
        });
    }
}
