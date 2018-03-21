<?php

namespace App\Http\Resources\Bond;

use Illuminate\Http\Resources\Json\JsonResource;

class UserBonds extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request); 
        return [
            'bond_id' => $this->id,
            'my_name' => $this->user->name,
            'partner_id' => $this->partner->id,
            'partner_name' => $this->partner->name
        ];
    }
}
