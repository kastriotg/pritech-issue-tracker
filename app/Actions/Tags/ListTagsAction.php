<?php

namespace App\Actions\Tags;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class ListTagsAction
{
    /**
     * @return array<string, mixed>
     */
    public function handle(Request $request): array
    {
        return [
            'tags' => Tag::query()
                ->withCount('issues')
                ->orderBy('name')
                ->paginate(10)
                ->through(fn (Tag $tag): array => TagResource::make($tag)->resolve($request)),
        ];
    }
}
