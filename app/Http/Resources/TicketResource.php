<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
        return tap(new TicketResourceCollection($resource), function ($collection) {
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
            "location"              => $this->location ?? "",
            "language"              => $this->language ?? "",
            "date"                  => $this->date ?? "",
            "strike"                => $this->strike ?? "",
            "strike_history"        => $this->strike_history ?? "",
            "ticket_history"        => $this->ticket_history ?? "",
            "remarks"               => $this->remarks ?? "",
            "ticket_status"         => $this->ticket_status ?? "",
            "total_replies"         => empty($this->replies) ? 0 : count($this->replies),
            "mono_multi_zd"         => $this->mono_multi_zd,
            "mono_multi_screeing"   => $this->mono_multi_screeing,
            "intakes_therapist"     => $this->intakes_therapist,
            "tresonit_number"       =>$this->tresonit_number,
            "datum_intake"          =>$this->datum_intake,
            "datum_intake_2"        =>$this->datum_intake_2,
            "nd_account"            =>$this->nd_account,
            "avc_alfmvm_sbg"        =>$this->avc_alfmvm_sbg,
            "honos"                 =>$this->honos,
            "status"                =>$this->status,
            "berha_intake"          =>$this->berha_intake,
            "rom_start"             =>$this->rom_start,
            "rom_end"               =>$this->rom_end,
            "berha_eind"            =>$this->berha_eind,
            "vtcb_date"             =>$this->vtcb_date,
            "closure"               =>$this->closure,
            "aanm_intake_1"         =>$this->aanm_intake_1,
            "assigned_user_id"      =>$this->assigned_user_id,
            "assigned_to_user_name" =>$this->assigned_to_user_name,
            "assigned_to_user_status"=>$this->assigned_to_user_status,
            "cancel_ticket_type"    =>$this->cancel_ticket_type,
            "cancel_reason"         =>$this->cancel_reason,
            "comments"              =>$this->comments,
            "upload_files"          => TicketUploadResource::collection($this->fileInfo),
            "assign_theparist"      => TicketAssignTherapistResource::collection($this->assignTherapist),
            "created_by"            => isset($this->created_by) ? (new AdminResource($this->createdBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "updated_by"            => isset($this->updated_by) ? (new AdminResource($this->updatedBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "patient_info"          => isset($this->patient) ?(new UserResource($this->patient))->hide(["created_by", "updated_by","group", "upload_files", "updated_by", "created_by"]) :null,
            "ticket_department_info"=> isset($this->ticketDepartment) ? (new TicketDepartmentResource($this->ticketDepartment))->hide(["created_by", "updated_by"]) : null,
            "replies"               => TicketReplyResource::collection($this->replies)->hide(["updated_by", "created_by"]),
           
        ]);
    }
}
