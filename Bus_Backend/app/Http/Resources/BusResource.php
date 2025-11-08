<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusResource extends JsonResource
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
            'bus_number' => $this->bus_number,
            'registration_no' => $this->registration_no,
            'model' => $this->model,
            'seating_capacity' => $this->seating_capacity,
            'status' => $this->status,
            'driver' => $this->driver ? new UserResource($this->driver) : null,
            'cleaner' => $this->cleaner ? new UserResource($this->cleaner) : null,
            'routes' => $this->routes ? $this->routes->map(function($route) {
                return [
                    'id' => $route->id,
                    'name' => $route->name,
                    'total_kilometer' => $route->total_kilometer,
                    'start_time' => $route->start_time,
                    'end_time' => $route->end_time,
                    'academic_year' => $route->academic_year,
                    'stops' => $route->stops ? $route->stops->map(function($stop) {
                        return [
                            'id' => $stop->id,
                            'name' => $stop->name,
                            'location' => $stop->location ?? null,
                            'route_id' => $stop->route_id,
                            'pickup_time' => $stop->pickup_time ?? null,
                            'drop_time' => $stop->drop_time ?? null,
                            'academic_year' => $stop->academic_year,
                            'distance_from_start_km' => $stop->distance_from_start_km ?? null,
                        ];
                    })->toArray() : []
                ];
            })->toArray() : [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}