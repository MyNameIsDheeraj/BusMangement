<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'student' => $this->student ? [
                'id' => $this->student->id,
                'name' => $this->student->user->name ?? null,
                'admission_no' => $this->student->admission_no
            ] : null,
            'bus' => $this->bus ? [
                'id' => $this->bus->id,
                'reg_no' => $this->bus->reg_no
            ] : null,
            'date' => $this->date,
            'status' => $this->status,
            'marked_by' => $this->markedBy ? new UserResource($this->markedBy) : null,
            'marked_at' => $this->marked_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}