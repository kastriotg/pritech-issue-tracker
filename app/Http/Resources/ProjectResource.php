<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'issues_count' => $this->whenCounted('issues'),
            'issues' => $this->when(
                $this->relationLoaded('issues'),
                fn (): array => $this->issues
                    ->map(fn ($issue): array => IssueResource::make($issue)->resolve($request))
                    ->all(),
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
            'created_at_label' => $this->created_at?->format('M j, Y'),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
