<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'success' => false,
            'message' => $this->message ?? 'An error occurred',
            'code' => $this->status_code ?? 400,
            'timestamp' => now()->toISOString(),
            'error' => $this->resource ?? null,
        ];

        // Add validation errors if present
        if ($this->resource && property_exists($this->resource, 'errors')) {
            $data['errors'] = $this->resource->errors;
        }

        return $data;
    }
}