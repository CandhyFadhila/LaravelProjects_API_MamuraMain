<?php

namespace App\Http\Resources\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'blog_category' => new BlogCategoryResource($this->blog_category),
            'thumbnail' => $this->documents,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'blog_content' => $this->blog_content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
