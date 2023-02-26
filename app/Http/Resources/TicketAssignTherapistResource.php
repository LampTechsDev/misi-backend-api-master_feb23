<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketAssignTherapistResource extends JsonResource
{
    protected $withoutFields = [];

    /**
     * Set Hidden Item 
     */
    public function hide(array $hide = []){
        $this->withoutFields = $hide;
        return $this;
    }

    /**
     * Filter Hide Items
     */
    protected function filter($data){
        return collect($data)->forget($this->withoutFields)->toArray();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->filter([

            "id"            => $this->id,
            "ticket_id"     => $this->ticket_id,
            "created_at"    => $this->created_at,
            "updated_at"    => $this->updated_at,
            "therapist"     => isset($this->therapist) ? (new TherapistResource($this->therapist))->hide(["created_by", "updated_by", "upload_files", "image", "therapist_type", "blood_group", "country", "state"]) :null,
        ]);
    }
}