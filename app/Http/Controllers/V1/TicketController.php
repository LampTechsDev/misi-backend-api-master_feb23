<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TicketReplyResource;
use App\Models\TicketReply;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $validator = Validator::make( $request->all(),[
                'patient_id'    => ['nullable', "exists:users,id"],
                'therapist_id'  => ['nullable', "exists:therapists,id"],
                "date"          => ["nullable", "date", "date_format:Y-m-d"]
            ]);

            if ($validator->fails()) {
                $this->apiOutput($this->getValidationError($validator), 200);
            }

            $tickets = Ticket::orderBy("date", "DESC")->orderBy("id", "DESC");
            if( !empty($request->date) ){
                $tickets->where("date", $request->date);
            }
            if( !empty($request->therapist_id) ){
                $tickets->where("therapist_id", $request->therapist_id);
            }
            if( !empty($request->patient_id) ){
                $tickets->where("patient_id", $request->patient_id);
            }

            $tickets = $tickets->get();
            //$this->data = TicketResource::collection($tickets)->hide(["replies", "created_by", "updated_by", "therapist", "patient"]);
            $this->data = TicketResource::collection($tickets)->hide(["replies", "created_by", "updated_by"]);
            $this->apiSuccess("Ticket Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        try{
           $validator = Validator::make( $request->all(),[
                'patient_id'    => ['nullable', "exists:users,id"],
                'therapist_id'  => ['nullable', "exists:therapists,id"],
                "ticket_department_id" => ["required"],
                "location"      => ["required", "string", "min:2"],
                "language"      => ["required", "string", "min:2"],
                "strike"        => ["required", "string"],
                "strike_history"=> ["nullable", "string"],
                "remarks"       => ["nullable", "string"],
                "ticket_history"=> ["nullable", "string"],
                "status"        => ["required", "boolean"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $ticket = new Ticket();            
            $ticket->patient_id = $request->patient_id ?? null;
            $ticket->therapist_id = $request->therapist_id ?? null;
            $ticket->ticket_department_id = $request->ticket_department_id;
            $ticket->location = $request->location ?? null;
            $ticket->language = $request->language ?? null;
            $ticket->date = now()->format("Y-m-d");
            $ticket->strike = $request->strike ?? null;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->status     = $request->status;
            $ticket->created_by = $request->user()->id ?? null;
            $ticket->mono_multi_zd = $request->mono_multi_zd ?? null ;
            $ticket->mono_multi_screeing = $request->mono_multi_screeing ?? null;
            $ticket->intakes_therapist = $request->intakes_therapist ?? null;
            $ticket->tresonit_number = $request->tresonit_number ?? null;
            $ticket->datum_intake = $request->datum_intake ?? null;
            $ticket->datum_intake_2 = $request->datum_intake_2 ?? null;
            $ticket->nd_account = $request->nd_account?? null;
            $ticket->avc_alfmvm_sbg = $request->avc_alfmvm_sbg?? null;
            $ticket->honos= $request->honos?? null;
            $ticket->berha_intake=$request->berha_intake?? null;
            $ticket->rom_start=$request->rom_start?? null;
            $ticket->rom_end=$request->rom_end?? null;
            $ticket->berha_eind=$request->berha_eind?? null;
            $ticket->vtcb_date=$request->vtcb_date?? null;
            $ticket->closure=$request->closure?? null;
            $ticket->aanm_intake_1=$request->aanm_intake_1?? null;
            $ticket->assigned_to_user_name=$request->assigned_to_user_name?? null;
            $ticket->assigned_to_user_status=$request->assigned_to_user_status?? null;
            $ticket->save();
            $this->apiSuccess();
            $this->data = (new TicketResource($ticket))->hide(["ticket_department", "updated_by", "created_by"]);
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try{
            $ticket = Ticket::find($request->id);
            if( empty($ticket) ){
                return $this->apiOutput("Ticket Data Not Found", 400);
            }
            $this->data = (new TicketResource($ticket));
            $this->apiSuccess("Ticket Detail Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:tickets,id"],
                'patient_id'    => ['nullable', "exists:users,id"],
                'therapist_id'  => ['nullable', "exists:therapists,id"],
                "location"      => ["nullable", "string"],
                "language"      => ["required", "string"],
                "strike"        => ["required", "string"],
                "strike_history"=> ["nullable", "string"],
                "remarks"       => ["nullable", "string"],
                "ticket_history"=> ["nullable", "string"],
                "status"        => ["required", "boolean"],
                "ticket_department_id" => ["required"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $ticket = Ticket::find($request->id);
            $ticket->patient_id = $request->patient_id ?? null;
            $ticket->therapist_id = $request->therapist_id ?? null;
            $ticket->ticket_department_id = $request->ticket_department_id;
            $ticket->location = $request->location ?? null;
            $ticket->language = $request->language ?? null;
            $ticket->date = now()->format("Y-m-d");
            $ticket->strike = $request->strike ?? null;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->status     = $request->status;
            $ticket->updated_by = $request->user()->id ?? null;
            $ticket->save();

            $this->apiSuccess("Ticket Info Updated successfully");
            $this->data = (new TicketResource($ticket))->hide(["replies", "created_by", "updated_by"]);
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Delete Ticket
     */
    public function deleteTicket(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            Ticket::where("id", $request->id)->delete();
            $this->apiSuccess("Ticket Deleted successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    

    /**
     * Ticket Reply List
     */
    public function replyList(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "ticket_id"     => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $reply = TicketReply::where("ticket_id", $request->ticket_id)
                ->orderBy("created_at", "desc")->get();
            $this->data = TicketReplyResource::collection($reply)->hide(["created_by", "updated_by"]);
            $this->apiSuccess("Ticket reply loaded successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
          
    }


    /**
     * Add Ticket Reply
     */
    public function addReply(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "ticket_id" => ["required", "exists:tickets,id"],
                "comment"   => ["nullable", "string"],
                "file"      => ["nullable", "file"],
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            if( empty($request->comment) && !$request->hasFile('file') ){
                return $this->apiOutput("Comment or File Upload is required", 200);
            }
    
            $reply = new TicketReply();
            $reply->ticket_id = $request->ticket_id;
            $reply->comment = $request->comment;
            if($request->hasFile('file')){
                $reply->file = $this->uploadFile($request, "file", $this->others_dir);
            }
            $reply->save();

            $this->apiSuccess("Ticket reply added successfully");
            $this->data = (new TicketReplyResource($reply));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
    /**
     * Edit Reply
     */
    public function editReply(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:ticket_replies,id"],
                "ticket_id"     => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            $reply = TicketReply::where("id", $request->id)
                ->where("ticket_id", $request->ticket_id)
                ->first();
            $this->data = (new TicketReplyResource($reply));
            $this->apiSuccess("Ticket reply loaded successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Update Reply
     */
    public function updateReply(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:ticket_replies,id"],
                "ticket_id" => ["required", "exists:tickets,id"],
                "comment"   => ["nullable", "string"],
                "file"      => ["nullable", "file"],
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            if( empty($request->comment) && $request->hasFile('file') ){
                $this->apiOutput("Comment or File Upload is required", 200);
            }
    
            $reply =  TicketReply::where("id", $request->id)->where("ticket_id", $request->ticket_id)->first();
            $reply->ticket_id = $request->ticket_id;
            $reply->comment = $request->comment;
            if($request->hasFile('file')){
                $reply->file = $this->uploadFile($request, "file", $this->others_dir, null, null, $reply->file);
            }
            $reply->save();

            $this->apiSuccess("Ticket reply Updated successfully");
            $this->data = (new TicketReplyResource($reply));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Delete Reply
     */
    public function deleteReply(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:ticket_replies,id"],
                "ticket_id"     => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            TicketReply::where("id", $request->id)->where("ticket_id", $request->ticket_id)->delete();
            $this->apiSuccess("Ticket reply Deleted successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}