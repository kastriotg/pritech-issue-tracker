<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Issue::query()->get()->each(fn (Issue $issue) => Comment::factory()
            ->count(2)
            ->for($issue)
            ->create());
    }
}
