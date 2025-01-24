<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonalRecord extends JsonResource
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
            'id'                    => $this->id,
            'user'                  => $this->user,
            'user_id'               => $this->user_id,
            'movement'              => $this->movement,
            'movement_id'           => $this->movement_id,
            'value'                 => $this->value,
            'dt_record'             => $this->dt_record instanceof \Carbon\Carbon ? $this->dt_record->format('d/m/Y') : \Carbon\Carbon::parse($this->dt_record)->format('d/m/Y'),
            'created_at'            => $this->created_at->format('d/m/Y'),
            'updated_at'            => $this->updated_at->format('d/m/Y'),
        ];
    }
}
