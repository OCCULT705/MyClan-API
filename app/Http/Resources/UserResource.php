<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use jeremykenedy\LaravelRoles\Models\Permission;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'details' => new MemberResource($this->whenLoaded('details')),
            'roles' => $this->roles,
            'permissions' => $this->permissions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
