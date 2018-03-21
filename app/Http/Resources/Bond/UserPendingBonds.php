<?php

namespace App\Http\Resources\Bond;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPendingBonds extends JsonResource
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
            'partner_id' => $this->sender->id,
            'partner_name' => $this->sender->name,
        ];
    }
}
