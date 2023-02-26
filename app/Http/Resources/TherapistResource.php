<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TherapistResource extends JsonResource
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
        return tap(new TherapistCollection($resource), function ($collection) {
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
            "first_name"        => $this->first_name,
            "last_name"         => $this->last_name,
            "email"             => $this->email,
            "phone"             => $this->phone,
            "address"           => $this->address,
            "language"          => $this->language,
            "bsn_number"        => $this->bsn_number,
            "dob_number"        => $this->dob_number,
            "insurance_number"  => $this->insurance_number,
            "emergency_contact" => $this->emergency_contact,
            "gender"            => $this->gender,
            "date_of_birth"     => $this->date_of_birth,
            "status"            => $this->status,
            //"image"             => $this->image,
            "image_url"         => asset($this->profile_pic),
            "therapist_type"    => isset($this->therapistType) ? (new TherapistTypeResource($this->therapistType))->hide(["created_by", "updated_by"]) : null,
            "blood_group"       => isset($this->blood) ? (new BloodGroupResource($this->blood))->hide(["created_by", "updated_by"]) : null,
            "country"           => isset($this->country) ? (new CountryResource($this->country))->hide(["created_by", "updated_by"]) : null,
            "state"             => isset($this->state) ? (new StateResource($this->state))->hide(["created_by", "updated_by"]) : null,
            "upload_files"      => TherapistUploadResource::collection($this->fileInfo),
            "created_by"        => isset($this->created_by) ? (new AdminResource($this->createdBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "updated_by"        => isset($this->updated_by) ? (new AdminResource($this->updatedBy))->hide(["groupId","department", "created_by","updated_by"]) : null
        ]);
    }
}
