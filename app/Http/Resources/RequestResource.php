<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->{'id'},
//            'user' => $this->whenLoaded('user'),
            'user_id' => $this->{'user_id'},
            'type' => $this->{'type'},
            'status' => $this->{'status'},
            'data' => $this->{'data'},
            'approved_at' => $this->{'approved_at'},
            'created_at' => $this->{'created_at'}
        ];
    }
}
