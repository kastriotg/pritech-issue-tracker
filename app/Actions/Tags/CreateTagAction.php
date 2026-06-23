<?php

namespace App\Actions\Tags;

use App\Models\Tag;

class CreateTagAction
{
    /**
     * @param  array{name: string, color?: string|null}  $attributes
     */
    public function handle(array $attributes): Tag
    {
        return Tag::query()->create($attributes);
    }
}
