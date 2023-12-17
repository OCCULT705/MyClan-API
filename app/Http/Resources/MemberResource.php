<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'fullname' => $this->fullname,
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'lastname' => $this->lastname,
            'givenname' => $this->givenname,
            'gender' => $this->gender,
            'birth' => $this->birth,
            'age' => $this->when(($this->death == null), function(){
                return $this->age;
            }),
            'death' => $this->when(($this->death !== null), function(){
                return $this->death;
            }),
            'address' => $this->address,
            'credentials' => new UserResource($this->credentials),
            'pictures' => PictureResource::collection($this->whenLoaded('pictures')),
            'father' => MemberResource::collection($this->whenLoaded('father')),
            'mother' => MemberResource::collection($this->whenLoaded('mother')),
            'spouses' => MemberResource::collection($this->whenLoaded('spouses')),
            'children' => MemberResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
