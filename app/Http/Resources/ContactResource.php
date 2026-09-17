<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender_label,
            'email' => $this->email,
            'tel' => $this->tel,
            'address' => $this->address,
            'building' => $this->building,
            'category' => [
                'id' => $this->category->id,
                'contact' => $this->category->content,
            ],

            'tags' => $this->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'tag' => $tag->name,
                ];
            }),

            'detail' => $this->detail,
        ];
    }
}
