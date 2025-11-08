<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'priority' => $this->priority,
            'student' => $this->student ? [
                'id' => $this->student->id,
                'name' => $this->student->user->name ?? null,
                'admission_no' => $this->student->admission_no
            ] : null,
            'submitted_by' => $this->submittedBy ? new UserResource($this->submittedBy) : null,
            'resolved' => $this->resolved,
            'resolved_at' => $this->resolved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}