<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
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
            "id"            => $this->id ?? "",
            "name"          => $this->name ?? "",
            "status"        => $this->status ?? "",
            "created_by"    => isset($this->created_by) ? (new AdminResource($this->createdBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "updated_by"    => isset($this->updated_by) ? (new AdminResource($this->updatedBy))->hide(["groupId","department", "created_by","updated_by"]) : null

        ]);
    }
}
