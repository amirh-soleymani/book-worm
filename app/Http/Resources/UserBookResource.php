<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBookResource extends JsonResource
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
            'book_id' => $this->book,
            'user_id' => $this->user_id,
            'font_size' => $this->font_size,
            'is_active' => $this->is_active,
            'last_page_id' => $this->last_page_id,
        ];
    }
}
