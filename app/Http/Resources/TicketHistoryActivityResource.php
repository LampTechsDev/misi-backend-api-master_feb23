<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketHistoryActivityResource extends JsonResource
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
    protected function filterFields($data){
        return collect($data)->forget($this->withoutFields)->toArray();
    }

    /**
     * Collection
     */
    public static function collection($resource){
        return tap(new TicketHistoryActivityCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->filterFields([
            "id"                => $this->id,
            "ticket_id "        => $this->ticket_id ,
            "assign_to_therapist"=> $this->assign_to_therapist,
            "appointment_group"=> $this->appointment_group,
            "call_strike"       => $this->call_strike,
            "strike_history"    => $this->strike_history,
            "ticket_history"    => $this->ticket_history,
            "status"            => $this->status,
            "language"          => $this->language,
            "assign_to_user"    => $this->assign_to_user,
            "assign_to_user_status"=> $this->assign_to_user_status,
            "deleted_by"=> $this->deleted_by,
            "deleted_date"=> $this->deleted_date,
            "remarks"=> $this->remarks,
            "modified_by"=> $this->modified_by,
            "modified_date"=> $this->modified_date,
            "created_by"   => $this->created_by,
            "created_date"   => $this->created_date,

        ]);
    }
}
