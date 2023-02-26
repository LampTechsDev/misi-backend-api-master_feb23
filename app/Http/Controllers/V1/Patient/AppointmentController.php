<?php

namespace App\Http\Controllers\V1\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\AppointmentUpload;
use App\Models\Appointmnet;
use App\Models\TherapistSchedule;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Get Current Table Model
     */
    private function getModel(){
        return new Appointmnet();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make( $request->all(),[
            "date"          => ["nullable", "date", "date_format:Y-m-d"],
            'therapist_id'    => ['nullable', "exists:therapists,id"],
        ]);
        
        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 200);
        }

        try{
            $patient = $request->user();
            $appoinement = Appointmnet::where("patient_id", $patient->id);
            if( !empty($request->date) ){
                $appoinement->where("date", $request->date);
            }
            if( !empty($request->therapist_id) ){
                $appoinement->where("therapist_id", $request->therapist_id);
            }
            $appoinement = $appoinement->orderBy("date", "ASC")->orderBy("start_time", "ASC")->get();
            
            $this->data = AppointmentResource::collection($appoinement);
            $this->apiSuccess("Appointment Load has been Successfully done");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'therapist_id'          => ['nullable', "exists:therapists,id"],
                "therapist_schedule_id" => ["required", "exists:therapist_schedules,id"],
                "status"                => ["required"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();

            $schedule = TherapistSchedule::where("id", $request->therapist_schedule_id)->first();
            if($schedule->status != "open"){
                return $this->apiOutput("Sorry! This time slot has been booked. You can't book this schedule. please try again.", 400);
            }
            $schedule->status = "booked";
            $schedule->patient_id = $request->patient_id;
            $schedule->save();

            $patient = $request->user();
            $data = $this->getModel();
            $data->therapist_id = $request->id;
            $data->patient_id   = $patient->id;
            $data->therapist_schedule_id = $request->therapist_schedule_id;
            $data->number       = $request->number;
            $data->history      = $request->history ?? null;
            $data->date         = $schedule->date;
            $data->start_time   = $schedule->start_time;
            $data->end_time     = $schedule->end_time;
            $data->fee          = $request->fee;
            $data->language     = $request->language;
            $data->type         = $request->type;
            $data->therapist_comment = $request->comment ?? null;
            $data->remarks      = $request->remarks ?? null;
            $data->status       = $request->status;
            if($request->hasFile('picture')){
                $data->image_url = $this->uploadFile($request, 'picture', $this->appointment_uploads, null,null,$data->image_url);
            }
            $data->save();
            $this->saveFileInfo($request, $data);
            
            DB::commit();
            $this->apiSuccess("Appointment Created Successfully");
            $this->data = (new AppointmentResource($data));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
            DB::rollBack();
        }
    }

     // Save File Info
     public function saveFileInfo($request, $appointment){
        $file_path = $this->uploadFile($request, 'file', $this->appointment_uploads, 720);
  
        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){
            $data = new AppointmentUpload();
            $data->appointment_id = $appointment->id;
            $data->file_name    = $request->file_name ?? "Appointment Upload";
            $data->file_url     = $path;
            $data->save();
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try{
            $patient = $request->user();
            $appointment = Appointmnet::where("id", $request->id)
                ->where("patient_id", $patient->id)->first();
            if( empty($appointment) ){
                return $this->apiOutput("Appointment Data Not Found", 400);
            }
            $this->data = (new AppointmentResource ($appointment));
            $this->apiSuccess("Appointment Detail Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                "id"                    => ["required", "exists:appointmnets,id"],
                'therapist_id'          => ['nullable', "exists:therapists,id"],
                "therapist_schedule_id" => ["required", "exists:therapist_schedules,id"],
                "status"                => ["required",]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $patient = $request->user();
            $appointment = Appointmnet::where("id", $request->id)
                ->where("patient_id", $patient->id)->first();
            if($appointment->therapist_schedule_id != $request->therapist_schedule_id){
                $schedule = TherapistSchedule::where("id", $appointment->therapist_schedule_id)
                    ->update(["status" => "open", "patient_id" => null]);
    
                $schedule->status = "open";
                $schedule->patient_id = null;
                $schedule->save();

                $schedule = TherapistSchedule::where("id", $request->therapist_schedule_id)->first();
                if($schedule->status != "open"){
                    return $this->apiOutput("Sorry! This time slot has been booked. You can't book this schedule. please try again.", 400);
                }
                $schedule->status = "booked";
                $schedule->patient_id = $patient->id;
                $schedule->save();
            }
           
            $appointment->patient_id   = $patient->id;
            $appointment->therapist_id = $request->therapist_id;
            $appointment->therapist_schedule_id = $request->therapist_schedule_id;
            //$appointment->number       = $request->number;
            $appointment->history      = $request->history ?? null;
            $appointment->fee          = $request->fee;
            $appointment->language     = $request->language;
            $appointment->type         = $request->type;
            $appointment->therapist_comment = $request->comment ?? null;
            $appointment->remarks      = $request->remarks ?? null;
            $appointment->status       = $request->status;
            if($request->hasFile('picture')){
                $appointment->image_url = $this->uploadFile($request, 'picture', $this->appointment_uploads, null,null,$appointment->image_url);
            }
            $appointment->save();
            $this->saveFileInfo($request, $appointment);
        
            DB::commit();
            $this->apiSuccess("Appointment Updated Successfully");
            $this->data = (new AppointmentResource($appointment));
            return $this->apiOutput();

        }catch(Exception $e){
            DB::rollBack();
            return $this->apiOutput($this->getError( $e), 500);
        }

    }

    public function assignedappointmentticketstatus(Request $request)
    {
    
        try{
        $validator = Validator::make(
            $request->all(),[
                "id"            => ["required", "exists:appointmnets,id"]
            ]);
            
           if ($validator->fails()) {    
                $this->apiOutput($this->getValidationError($validator), 200);
           }
            $patient = $request->user();
            $ticket = Appointmnet::where("id", $request->id)
                ->where("patient_id", $patient->id)->first();
            $ticket->appointment_ticket_status ="Cancelled";
            $ticket->save();
            $this->apiSuccess("Assigned Ticket Cancelled successfully");
            $this->data = (new AppointmentResource($ticket));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
            $patient = $request->user();
            $appointment = Appointmnet::where("id", $request->id)
                ->where("patient_id", $patient->id)->first();
            $appointment->delete();
            $this->apiSuccess();
            return $this->apiOutput("Appointment Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    public function updateAppointmentFileInfo(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:appointment_uploads,id"],

            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $data = AppointmentUpload::find($request->id);
            
            if($request->hasFile('picture')){
                $data->file_url = $this->uploadFile($request, 'picture', $this->appointment_uploads, null,null,$data->file_url);
            }

            $data->save();
          
            $this->apiSuccess("Appointment File Updated Successfully");
            return $this->apiOutput();
           
           
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}
