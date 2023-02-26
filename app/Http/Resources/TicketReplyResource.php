<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketReplyResource extends JsonResource
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
        return tap(new TicketReplyCollection($resource), function ($collection) {
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
            "ticket_id"         => $this->ticket_id,
            "comment"           => $this->comment ?? "",
            "file"              => !empty($this->file) ? asset($this->file) : null,
            "created_at"        => $this->created_at,
            "updated_at"         =>$this->updated_at,
            "created_by"    => isset($this->created_by) ? (new AdminResource($this->createdBy))->hide(["groupId","created_by","updated_by"]) : null,
            "updated_by"    => isset($this->updated_by) ? (new AdminResource($this->updatedBy))->hide(["groupId","created_by","updated_by"]) : null
        ]);
    }
}
