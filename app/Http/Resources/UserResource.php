<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends JsonResource
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
            'first_name' => $this->{'first_name'},
            'last_name' => $this->{'last_name'},
            'email' => $this->{'email'},
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->{'created_at'},
        ];
    }
}
