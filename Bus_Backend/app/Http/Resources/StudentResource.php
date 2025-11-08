<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'class' => $this->class ? [
                'id' => $this->class->id,
                'name' => $this->class->name,
                'teacher' => $this->class->classTeacher ? [
                    'id' => $this->class->classTeacher->id,
                    'name' => $this->class->classTeacher->name ?? null
                ] : null
            ] : null,
            'admission_no' => $this->admission_no,
            'address' => $this->address,
            'pickup_stop' => $this->pickupStop ? [
                'id' => $this->pickupStop->id,
                'name' => $this->pickupStop->name,
                'location' => $this->pickupStop->location,
                'time' => $this->pickupStop->time
            ] : null,
            'drop_stop' => $this->dropStop ? [
                'id' => $this->dropStop->id,
                'name' => $this->dropStop->name,
                'location' => $this->dropStop->location,
                'time' => $this->dropStop->time
            ] : null,
            'parents' => $this->parents->map(function($parent) {
                return [
                    'id' => $parent->id,
                    'name' => $parent->user->name ?? null,
                    'email' => $parent->user->email ?? null,
                    'mobile' => $parent->user->mobile ?? null
                ];
            }),
            'bus_service_active' => $this->bus_service_active,
            'academic_year' => $this->academic_year,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}