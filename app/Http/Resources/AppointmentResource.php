<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AppointmentResource extends JsonResource
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
        return tap(new AppointmentCollection($resource), function ($collection) {
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
            "id"         => $this->id,
            "appointmentnumber"     => $this->appointmentnumber,
            "history"    => $this->history,
            "trx_type"   => $this->trx_type,
            "date"       => $this->date,
            "time"       => $this->time,
            "fee"        => $this->fee,
            "language"   => $this->language,
            "type"       => $this->type,
            "therapist_comment" => $this->therapist_comment,
            "remarks"           => $this->remarks,
            "status"            => $this->status,
            "total_intake"      => $this->total_intake,
            "image"             => $this->image,
            "image_url"         => asset($this->image_url),
            "invoice_url"       => !empty($this->invoice_url) && Storage::disk("public")->exists($this->invoice_url) ? asset(Storage::disk("public")->url($this->invoice_url)) : "",
            "upload_files"      => AppointmentUploadResource::collection($this->fileInfo),
            "cancel_appointment_type" => $this->cancel_appointment_type,
            "cancel_reason"     => $this->cancel_reason,
            "appointment_ticket_status" => $this->appointment_ticket_status,
            "patient_info"          => isset($this->patient) ? (new UserResource($this->patient))->hide(["created_by", "updated_by"]) : null,
            "ticket"                => isset($this->ticket) ? (new TicketResource($this->ticket))->hide(["upload_files", "created_by", "updated_by", "therapist_info", "patient_info", "ticket_department_info", "replies"]) : null,
            "therapist_info"        => isset($this->therapist)? (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]) : null,
            "therapist_schedule"    => isset($this->schedule)? (new TherapistScheduleResource($this->schedule))->hide(["created_by", "updated_by"]) : null,
            "intake"                =>  IntakeResource::collection($this->intake),
        ]);
    }
}
