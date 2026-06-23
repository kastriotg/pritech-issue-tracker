<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'issue_id' => $this->issue_id,
            'author_name' => $this->author_name,
            'body' => $this->body,
            'created_at' => $this->created_at?->toISOString(),
            'created_at_label' => $this->created_at?->format('M j, Y g:i A'),
        ];
    }
}
