<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Bug', 'color' => '#ff2525'],
            ['name' => 'Feature', 'color' => '#5898ff'],
            ['name' => 'Documentation', 'color' => '#07eb9f'],
            ['name' => 'Question', 'color' => '#ffb638'],
            ['name' => 'Urgent', 'color' => '#b797ff'],
        ])->each(fn (array $tag) => Tag::query()->updateOrCreate(
            ['name' => $tag['name']],
            ['color' => $tag['color']],
        ));
    }
}
