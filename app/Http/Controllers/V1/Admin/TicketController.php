<?php

namespace App\Http\Controllers\V1\Admin;

use Exception;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TicketReplyResource;
use App\Models\TicketReply;
use App\Models\TicketUpload;
use Illuminate\Support\Facades\DB;
use App\Http\Components\Classes\Facade\ActivityLog;
use App\Http\Components\Traits\TherapistTicket;
use App\Http\Resources\UserActivityResource;
use App\Models\UserActivity;

class TicketController extends Controller
{
    use TherapistTicket;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            if(!PermissionController::hasAccess("ticket_list")){
                return $this->apiOutput("Permission Missing", 403);
            }

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
                $tickets->whereHas("assignTherapist", function($qry) use($request){
                    $qry->where("therapist_id", $request->therapist_id);
                });
            }
            if( !empty($request->patient_id) ){
                $tickets->where("patient_id", $request->patient_id);
            }

            $tickets = $tickets->get();
            
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
            if(!PermissionController::hasAccess("ticket_create")){
                return $this->apiOutput("Permission Missing", 403);
            }
           $validator = Validator::make( $request->all(),[
                'patient_id'    => ['nullable', "exists:users,id"],
                'therapist_id'  => ['nullable', "array"],
                "therapist_id.*" => ["exists:therapists,id"],
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
            DB::beginTransaction();
            $ticket = new Ticket();            
            $ticket->patient_id = $request->patient_id ?? null;
            $ticket->ticket_department_id = $request->ticket_department_id;
            $ticket->location = $request->location ?? null;
            $ticket->ticket_status     = $request->ticket_status;
            $ticket->language = $request->language ?? null;
            $ticket->date = now()->format("Y-m-d");
            $ticket->strike = $request->strike ?? null;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->comments = $request->comments ?? null;
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
            $ticket->assigned_user_id=$request->assigned_user_id?? null;
            $ticket->assigned_to_user_name=$request->assigned_to_user_name?? null;
            $ticket->assigned_to_user_status=$request->assigned_to_user_status?? null;
            $ticket->save();
            $this->AssignTherapistIntoTicket($ticket->id, $request->therapist_id);
            $this->saveFileInfo($request, $ticket);

            ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket ID: ". $ticket->id." Created Successfully");

            DB::commit();
            $this->apiSuccess("Ticket Create Successfully");
            $this->data = (new TicketResource($ticket))->hide(["ticket_department", "updated_by", "created_by"]);
            
            ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Created Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    // Save File Info
    public function saveFileInfo($request, $ticket){
        $file_path = $this->uploadFile($request, 'file', $this->ticket_uploads, 720);
  
        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){
            $data = new TicketUpload();
            $data->ticket_id = $ticket->id;
            $data->file_name    = $request->file_name ?? "Ticket Upload";
            $data->file_url     = $path;
            $data->save();
        }
    }


    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        
        try{
            if(!PermissionController::hasAccess("ticket_show")){
                return $this->apiOutput("Permission Missing", 403);
            }
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
            if(!PermissionController::hasAccess("ticket_update")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:tickets,id"],
                'patient_id'    => ['nullable', "exists:users,id"],
                'therapist_id'  => ['nullable', "array"],
                "therapist_id.*" => ["exists:therapists,id"],
                "location"      => ["nullable", "string"],
                "language"      => ["required", "string"],
                "strike"        => ["required", "string"],
                "strike_history"=> ["nullable", "string"],
                "remarks"       => ["nullable", "string"],
                "ticket_history"=> ["nullable", "string"],
                "status"        => ["required", "boolean"],
                "ticket_department_id" => ["required"],
                //"file"          => ["nullable", "file"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $ticket = Ticket::find($request->id);
            $ticket->patient_id = $request->patient_id ?? null;
            
            if($ticket->ticket_department_id != $request->ticket_department_id)
            {
                $ticket->assigned_to_user_name = null;
                $ticket->assigned_to_user_status= null;

                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket ID ".$ticket->id. " department updated Successfully");
            }
            
           
            $ticket->ticket_department_id = $request->ticket_department_id;
           
            if($ticket->location != $request->location){
                $ticket->location = $request->location;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Location ".$ticket->location. " updated Successfully");
            }
           
           //ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket ID ".$ticket->location. " department updated Successfully");
            
           
            //$ticket->location = $request->location;
            if($ticket->language != $request->language ) {
                $ticket->language = $request->language;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Language ".$ticket->language. " updated Successfully");
            }  
          
            $ticket->date = now()->format("Y-m-d");
            if($ticket->strike !=$request->strike){
                $ticket->strike = $request->strike;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Strike ".$ticket->strike. " updated Successfully");
            }

            if($ticket->strike_history != $request->strike_history){
                $ticket->strike_history=$request->strike_history;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Strike History ".$ticket->strike_history. " updated Successfully");
            }
            
            if($ticket->ticket_history != $request->ticket_history){
                $ticket->ticket_history=$request->ticket_history;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket History ".$ticket->ticket_history. " updated Successfully");

            }
            
            if($ticket->remarks != $request->remarks){
                $ticket->remarks=$request->remarks;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Remarks ".$ticket->remarks. " updated Successfully");
            }
            
            if($ticket->status != $request->status){
                
                $ticket->status=$request->status;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Status ".$ticket->status. " updated Successfully");
            }
            
            $ticket->updated_by = $request->user()->id ?? null;
            if($ticket->mono_multi_zd != $request->mono_multi_zd ){
                $ticket->mono_multi_zd = $request->mono_multi_zd;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket mono_multi_zd ".$ticket->mono_multi_zd. " updated Successfully");
            }
            
            if($ticket->mono_multi_screeing != $request->mono_multi_screeing ){
                $ticket->mono_multi_screeing = $request->mono_multi_screeing;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket mono_multi_screeing ".$ticket->mono_multi_screeing. " updated Successfully");
            }
            
            if($ticket->intakes_therapist != $request->intakes_therapist){
                $ticket->intakes_therapist != $request->intakes_therapist;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket intakes_therapist ".$ticket->intakes_therapist. " updated Successfully");
            }
            
            if( $ticket->tresonit_number != $request->tresonit_number){
                $ticket->tresonit_number =$request->tresonit_number;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket tresonit_number ".$ticket->tresonit_number. " updated Successfully");
            }
           
            if( $ticket->datum_intake != $request->datum_intake ){
                $ticket->datum_intake = $request->datum_intake;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket datum_intake ".$ticket->datum_intake. " updated Successfully");

            }
            if( $ticket->datum_intake_2 != $request->datum_intake_2 ){
                $ticket->datum_intake_2 = $request->datum_intake_2;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket datum_intake_2 ".$ticket->datum_intake_2. " updated Successfully");
            }
            
            if($ticket->nd_account != $request->nd_account){
                $ticket->nd_account=$request->nd_account;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket nd_account ".$ticket->nd_account. " updated Successfully");
            }
            
            if($ticket->avc_alfmvm_sbg != $request->avc_alfmvm_sbg){
                $ticket->avc_alfmvm_sbg = $request->avc_alfmvm_sbg;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket avc_alfmvm_sbg ".$ticket->avc_alfmvm_sbg. " updated Successfully");
            }
            
            if($ticket->honos != $request->honos){
                $ticket->honos = $request->honos;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket honos ".$ticket->honos. " updated Successfully");
            }
            
            if($ticket->berha_intake !=$request->berha_intake){
                $ticket->berha_intake = $request->berha_intake;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket berha_intake ".$ticket->berha_intake. " updated Successfully");
            }
            
            if($ticket->rom_start !=$request->rom_start){
                $ticket->rom_start =$request->rom_start;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket rom_start ".$ticket->rom_start. " updated Successfully");

            }
            
            if($ticket->rom_end !=$request->rom_end){
                $ticket->rom_end =$request->rom_end;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket rom_end ".$ticket->rom_end . " updated Successfully");
            }
           
            if($ticket->berha_eind !=$request->berha_eind){
                $ticket->berha_eind =$request->berha_eind;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket berha_eind ".$ticket->berha_eind . " updated Successfully");
            }
            
            if($ticket->vtcb_date !=$request->vtcb_date){
                $ticket->vtcb_date =$request->vtcb_date;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket vtcb_date ".$ticket->vtcb_date . " updated Successfully");

            }
            
            if( $ticket->closure !=$request->closure){
                $ticket->closure =$request->closure;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket closure ".$ticket->closure . " updated Successfully");

            }
           
            if( $ticket->aanm_intake_1 !=$request->aanm_intake_1){
                $ticket->aanm_intake_1 =$request->aanm_intake_1;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket aanm_intake_1 ".$ticket->aanm_intake_1 . " updated Successfully");
            }
            if( $ticket->comments !=$request->comments){
                $ticket->comments =$request->comments;
                ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket comment ".$ticket->comments . " updated Successfully");
            }
            //$ticket->assigned_to_user_name=$request->assigned_to_user_name?? null;
            //$ticket->assigned_to_user_status=$request->assigned_to_user_status?? null;
            //$ticket->file = $this->uploadFile($request, "file", $this->others_dir, null, null, $ticket->file);
            $ticket->save();
            $this->AssignTherapistIntoTicket($ticket->id, $request->therapist_id);
            //ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Updated Successfully");

            $this->apiSuccess("Ticket Info Updated successfully");
            $this->data = (new TicketResource($ticket))->hide(["replies", "created_by", "updated_by"]);
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
     
    /**
     * Assigned Ticket Yourself
     */
    public function assignedticket(Request $request)
    {  
        try{
            if(!PermissionController::hasAccess("assignedticket")){
                return $this->apiOutput("Permission Missing", 403);
            }
         $validator = Validator::make(
            $request->all(),[
                "id"            => ["required", "exists:tickets,id"]
            ]);
           if ($validator->fails()) {
                $this->apiOutput($this->getValidationError($validator), 200);
           }
            $ticket = Ticket::find($request->id);
            $ticket->date = now();
            $ticket->updated_by = $request->updated_by;
            $ticket->assigned_to_user_name = $request->assigned_to_user_name;
            $ticket->assigned_to_user_status =$request->assigned_to_user_status;
            $ticket->save();
            $this->apiSuccess("Assigned Ticket Info Updated successfully");
            $this->data = (new TicketResource($ticket));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Cancel Ticket
     */
    public function cancelticket(Request $request)
    {
        
        try{
            if(!PermissionController::hasAccess("cancelticket")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make($request->all(),[
                "id"            => ["required", "exists:tickets,id"]
            ]);

           if ($validator->fails()) {
                $this->apiOutput($this->getValidationError($validator), 200);
           }
            $ticket = Ticket::find($request->id);
            $ticket->ticket_status =$request->ticket_status;
            $ticket->cancel_ticket_type=$request->cancel_ticket_type;
            $ticket->cancel_reason=$request->cancel_reason;
            $ticket->save();
            $this->apiSuccess("Ticket cancelled successfully");
            $this->data = (new TicketResource($ticket))->hide(["therapist_info","patient_info", "created_by", "updated_by"]);
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
            if(!PermissionController::hasAccess("ticket_delete")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $ticket = Ticket::where("id", $request->id)->first();
            TicketUpload::where('id',$request->id)->delete();
            $ticket->delete();
            $this->apiSuccess("Ticket Deleted successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    public function deleteFileTicket(Request $request){
        try{
            if(!PermissionController::hasAccess("deleteFileTicket")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:ticket_uploads,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            // $ticket = Ticket::where("id", $request->id)->first();
            //$this->RemoveFile($this->$ticket);
            $ticketupload=TicketUpload::where('id',$request->id);
            $ticketupload->delete();
            $this->apiSuccess("Ticket File Deleted successfully");
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
            if(!PermissionController::hasAccess("ticket_replyList")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "ticket_id"     => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $reply = TicketReply::where("ticket_id", $request->ticket_id)
                ->orderBy("created_at", "desc")->get();
            //$this->data = TicketReplyResource::collection($reply)->hide(["created_by", "updated_by"]);
            $this->data = TicketReplyResource::collection($reply);
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
            if(!PermissionController::hasAccess("ticket_addReply")){
                return $this->apiOutput("Permission Missing", 403);
            }
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
            $reply->created_by = $request->user()->id;
            $reply->updated_by = $request->user()->id;
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
            if(!PermissionController::hasAccess("ticket_editReply")){
                return $this->apiOutput("Permission Missing", 403);
            }
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
            $reply->updated_by = $request->user()->id;
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
            if(!PermissionController::hasAccess("ticket_updateReply")){
                return $this->apiOutput("Permission Missing", 403);
            }
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
            if(!PermissionController::hasAccess("ticket_deleteReply")){
                return $this->apiOutput("Permission Missing", 403);
            }
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
    
    public function ticketHistoryActivity(Request $request)
    {
        try{
            if(!PermissionController::hasAccess("ticket_ticketHistoryActivity")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $ticketactivity = UserActivity::where("tableable_type", (new Ticket())->getMorphClass())->orderBy('id', "DESC");
            if( !empty($request->ticket_id) ){
                $ticketactivity->where("tableable_id", $request->ticket_id);
            }
            $ticketactivity = $ticketactivity->get();

            $this->data = UserActivityResource::collection($ticketactivity);
            $this->apiSuccess("Ticket History Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    
     public function ticketHistoryActivityshow(Request $request)
      {
        try{
            if(!PermissionController::hasAccess("ticket_ticketHistoryActivityshow")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $ticket = UserActivity::where("tableable_type", (new Ticket())->getMorphClass())
                ->where("tableable_id", $request->ticket_id)->get();
                
            $this->data = UserActivityResource::collection($ticket);
            $this->apiSuccess("Ticket History Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
     }

     public function addFileTicket(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "ticket_id"            => ["required","exists:tickets,id"],

            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $this->saveAddFileInfo($request);
            $this->apiSuccess("Ticket File Added Successfully");
            return $this->apiOutput();
           
           
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Save File Info
     */
    public function saveAddFileInfo($request){

        $file_path = $this->uploadFile($request, 'file', $this->ticket_uploads,720);
        
        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){

                $data = new TicketUpload();
                //$data->created_by   = $request->user()->id;
                $data->ticket_id   = $request->ticket_id;
                $data->file_name    = $request->file_name ?? "Ticket Upload";
                $data->file_url     = $path;
                //$data->file_type    = $request->file_type ;
                //$data->status       = $request->status;
                //$data->remarks      = $request->remarks ?? '';
                $data->save();            

            }
      
    }

    public function updateTicketFileInfo(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:ticket_uploads,id"],

            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $data = TicketUpload::find($request->id);
            
            if($request->hasFile('picture')){
                $data->file_url = $this->uploadFile($request, 'picture', $this->ticket_uploads, null,null,$data->file_url);
            }

            $data->save();
          
            $this->apiSuccess("Ticket File Updated Successfully");
            return $this->apiOutput();
           
           
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
    
}
