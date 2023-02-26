<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PitFormulaResource extends JsonResource
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
        return tap(new PitFormulaCollection($resource), function ($collection) {
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
            "id"                    => $this->id,            
            "name"                  => $this->name,
            "type_of_legitimation"  => $this->type_of_legitimation,
            "document_number"       => $this->document_number,
            "identify_expire_date"  => $this->identify_expire_date,
            "status"                => $this->status,
            "remarks"               => $this->remarks,
            "created_at"            => $this->created_at,
            "updated_at"            => $this->updated_at,
            
            "patient"               => isset($this->patient) ? (new UserResource($this->patient))->hide(["country", "updated_by","created_by","state","upload_files"]) : null,
            "ticket"                => isset($this->Ticket) ? (new TicketResource($this->Ticket))->hide(["upload_files", "created_by", "updated_by", "therapist_info", "patient_info", "ticket_department_info", "replies"]) : null,
            "created_by"            => isset($this->createdBy) ? (new AdminResource($this->createdBy))->hide(["department", "created_by", "updated_by"]) : null,
            "updated_by"            => isset($this->updatedBy) ? (new AdminResource($this->updatedBy))->hide(["department", "created_by", "updated_by"]) : null,
        ]);
    }
}
