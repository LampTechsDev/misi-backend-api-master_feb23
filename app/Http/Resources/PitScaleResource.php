<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PitScaleResource extends JsonResource
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
        return tap(new PitScaleCollection($resource), function ($collection) {
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
            "id"            => $this->id,
            "scale_value"   => $this->scale_value ?? 0,
            "status"        => $this->status,
            "remarks"       => $this->remarks,
            "created_at"    => $this->created_at,
            "updated_at"    => $this->updated_at,
            
            "patient"       => isset($this->patient) ? (new UserResource($this->patient))->hide(["country", "updated_by","created_by","state","upload_files"]) : null,
            "pit_formula"   => isset($this->pitformula) ? (new PitFormulaResource($this->pitformula))->hide(["patient", "created_by", "updated_by", "ticket"]) : null,
            "question"      => isset($this->question) ? (new QuestionResource($this->question))->hide(["created_by", "updated_by"]) : null,
            "created_by"    => isset($this->createdBy) ? (new AdminResource($this->createdBy))->hide(["department", "created_by", "updated_by"]) : null,
            "updated_by"    => isset($this->updatedBy) ? (new AdminResource($this->updatedBy))->hide(["department", "created_by", "updated_by"]) : null,
        ]);
    }
}