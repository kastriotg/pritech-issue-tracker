<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'name' => $this->name,
            'color' => $this->color,
            'issues_count' => $this->whenCounted('issues'),
            'created_at_label' => $this->whenHas(
                'created_at',
                fn (): ?string => $this->created_at?->format('M j, Y'),
            ),
        ];
    }
}
