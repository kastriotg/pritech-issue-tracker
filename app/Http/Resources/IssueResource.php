<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => str($this->status)->replace('_', ' ')->title()->toString(),
            'priority' => $this->priority,
            'priority_label' => str($this->priority)->title()->toString(),
            'due_date' => $this->due_date?->toDateString(),
            'due_date_label' => $this->due_date?->format('M j'),
            'created_at_label' => $this->created_at?->format('M j, Y'),
            'project' => $this->when(
                $this->relationLoaded('project'),
                fn (): array => [
                    'id' => $this->project->id,
                    'name' => $this->project->name,
                ],
            ),
            'comments_count' => $this->whenCounted('comments'),
            'tags' => $this->when(
                $this->relationLoaded('tags'),
                fn (): array => $this->tags
                    ->map(fn ($tag): array => TagResource::make($tag)->resolve($request))
                    ->all(),
            ),
            'tag_badges' => collect($this->resource->getAttributes()['tag_badges'] ?? [])->map(fn ($tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color,
            ])->values()->all(),
        ];
    }
}
