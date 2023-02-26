<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TherapistScheduleSettingsResource extends JsonResource
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


    public function toArray($request)
    {
        return $this->filterFields([
            "id"            => $this->id ?? "",
            "interval_time" => $this->interval_time ?? "",
            "start_time"    => $this->start_time ?? "",
            "end_time"      => $this->end_time ?? "",
            "holiday"       => $this->holiday ?? [],
            "therapist"     => isset($this->therapist) ? (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]) : null,
        ]);
    }
}
