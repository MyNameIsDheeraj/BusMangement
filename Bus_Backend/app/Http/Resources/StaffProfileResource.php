<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffProfileResource extends JsonResource
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
            'user' => new UserResource($this->user),
            'position' => $this->position,
            'salary' => $this->salary,
            'hire_date' => $this->hire_date,
            'emergency_contact' => $this->emergency_contact,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}