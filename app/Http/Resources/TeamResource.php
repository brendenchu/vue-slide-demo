<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * NOTE: The 'current' field will query the database if user's teams aren't eager-loaded.
     * For optimal performance, eager-load 'teams' relationship on the User model.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'slug' => $this->key,
            'name' => $this->label,
            'description' => $this->description,
            'status' => $this->status->label(),
            'current' => $this->when($request->user(), function () use ($request) {
                $currentTeam = $request->user()->currentTeam();

                return $currentTeam?->is($this->resource) ?? false;
            }),
        ];
    }
}
