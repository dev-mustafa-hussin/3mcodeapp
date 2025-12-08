<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'parent_name' => $this->whenLoaded('parent', function () {
                return $this->parent->name;
            }),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
