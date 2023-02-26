<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TherapistScheduleResource extends JsonResource
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
        return tap(new TherapistScheduleCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }

    public function toArray($request)
    {
        return $this->filterFields([
            "id"            => $this->id,
            "date"          => $this->date,
            "start_time"    => $this->start_time,
            "end_time"      => $this->end_time,
            "status"        => $this->status,
            "remarks"       => $this->remarks,
            "cancel_reason" => $this->cancel_reason,
            "patient"       => isset($this->patient) ? (new UserResource($this->patient))->hide(["created_by", "updated_by", "upload_files", "group"]) : null,
            "therapist"     => isset($this->therapist) ? (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]) : null,
            "created_by"    => isset($this->created_by) ? (new AdminResource($this->createdBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "updated_by"    => isset($this->updated_by) ? (new AdminResource($this->updatedBy))->hide(["groupId","department", "created_by","updated_by"]) : null
        ]);
    }
}
