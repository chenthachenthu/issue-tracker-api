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

        'projectid' => $this->id,
        'ProjectName' => $this->name,
        'Description' => $this->description,
        'task_count' => $this->whenCounted('tasks'),
        'created_at' => $this->created_at->toIso8601String()

        ];    

    }
}
