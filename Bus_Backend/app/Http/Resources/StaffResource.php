<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
            'license_number' => $this->license_number,
            'salary' => $this->salary,
            'emergency_contact' => $this->emergency_contact,
            'bus' => $this->bus ? [
                'id' => $this->bus->id,
                'reg_no' => $this->bus->reg_no
            ] : null,
            'hire_date' => $this->created_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}