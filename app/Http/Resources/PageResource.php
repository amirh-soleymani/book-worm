<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'book_id' => $this['book_id'],
            'page_number' => $this['page_number'],
            'content' => $this['content'],
            'font_size' => $this['font_size'],
        ];
    }
}
