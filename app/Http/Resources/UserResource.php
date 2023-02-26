<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class UserResource extends JsonResource
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
        return tap(new UserCollection($resource), function ($collection) {
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
            "source"            => $this->source,
            "first_name"        => $this->first_name,
            "last_name"         => $this->last_name,
            "email"             => $this->email,
            "phone"             => $this->phone,
            "patientstatus"     => $this->patientstatus,
            "alternet_phone"    => $this->alternet_phone,
            "address"           => $this->address,
            "area"              => $this->area,
            "city"              => $this->city,
            "bsn_number"        => $this->bsn_number,
            "dob_number"        => $this->dob_number,
            "insurance_number"  => $this->insurance_number,
            "emergency_contact" => $this->emergency_contact,
            "age"               => $this->age,
            "gender"            => $this->gender,
            "marital_status"    => $this->marital_status,
            "medical_history"   => $this->medical_history,
            "date_of_birth"     => $this->date_of_birth,
            "occupation"        => $this->occupation,
            "remarks"           => $this->remarks,
            "image"             => $this->image,
            "image_url"         => asset($this->image_url),
            "blood_group"       => (new BloodGroupResource($this->blood))->hide(["created_by", "updated_by"]),
            "country"           => (new CountryResource($this->country))->hide(["created_by", "updated_by"]),
            "state"             => (new StateResource($this->state))->hide(["created_by", "updated_by"]),
            "created_by"        => isset($this->created_by) ? (new AdminResource($this->createdBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "updated_by"        => isset($this->updated_by) ? (new AdminResource($this->updatedBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "upload_files"      => PatientUploadResource::collection($this->fileInfo),
        ]);
    }
}
