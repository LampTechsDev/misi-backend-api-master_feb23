<?php

namespace App\Http\Controllers\V1;

use App\Events\Appointment;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\AppointmentUpload;
use App\Models\Appointmnet;
use App\Models\TherapistSchedule;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

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
        try{
            $validator = Validator::make($request->all(), [
                "date"                  => ["nullable", "date", "date_format:Y-m-d"],
                "therapist_id"          => ["nullable", "exists:therapists,id"],
                "patient_id"            => ["nullable", "exists:users,id"],
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            $appoinement = Appointmnet::orderBy('date', "ASC")->orderBy("start_time", "ASC");
            if( !empty($request->date) ){
                $appoinement->where("date", $request->date);
            }
            else{
                $appoinement->where("date", ">=", now()->format('Y-m-d'));
            }
            if( !empty($request->patient_id) ){
                $appoinement->where("patient_id", $request->patient_id);
            }
            if( !empty($request->therapist_id) ){
                $appoinement->where("therapist_id", $request->therapist_id);
            }
            $appoinement = $appoinement->get();
            $this->data = AppointmentResource::collection($appoinement)->hide(["intake"]);
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
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                "therapist_id"  => ["required", "exists:therapists,id"],
                "patient_id"    => ["required", "exists:users,id"],
                //"ticket_id"  => ["required", "exists:tickets,id"],
                "therapist_schedule_id" => ["required", "exists:therapist_schedules,id"],
                "status"        => ["required", "boolean"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }


            $schedule = TherapistSchedule::where("id", $request->therapist_schedule_id)->first();
            if($schedule->status != "open"){
                return $this->apiOutput("Sorry! This time slot has been booked. You can't book this schedule. please try again.", 400);
            }
            $schedule->status = "booked";
            $schedule->patient_id = $request->patient_id;
            $schedule->save();

            $data = $this->getModel();
            $data->created_by   = $request->user()->id;
            $data->therapist_id = $request->therapist_id;
            $data->patient_id   = $request->patient_id;
            $data->ticket_id    = $request->ticket_id ?? null;
            $data->trx_type     = $request->trx_type;
            $data->therapist_schedule_id = $request->therapist_schedule_id;
            $number      = Appointmnet::max('appointmentnumber')+1000;
            $data->appointmentnumber = $number;
            
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
            $data->total_intake = $request->total_intake ?? null;
            if($request->hasFile('picture')){
                $data->image_url = $this->uploadFile($request, 'picture', $this->appointment_uploads, null,null,$data->image_url);
            }
            $data->invoice_url = $this->generatingInvoice($data);
            $data->save();
            $this->saveFileInfo($request, $data);
            
            try{
                event(new Appointment($data));
            }catch(Exception $e){
                
            }
            DB::commit();
            $this->apiSuccess("Appointment Created Successfully");
            $this->data = (new AppointmentResource($data))->hide(["intake"]);
            return $this->apiOutput();
        }catch(Exception $e){
            DB::rollBack();
            return $this->apiOutput($this->getError($e), 500);
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
     * Generating Invoice
     */
    protected function generatingInvoice($appointment){
        if(!empty($appointment->invoice_url) && Storage::disk("public")->exists($appointment->invoice_url)){
            Storage::disk("public")->delete($appointment->invoice_url);
        }

        $params = [
            "data"      => $appointment,
            "patient"   => $appointment->patient ?? null,
            "therapist" => $appointment->therapist ?? null,
        ];
        $invoice_html = view('invoice.appointment-invoice', $params)->render();
        $file_name = $appointment->appointmentnumber .'.pdf';
        
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($invoice_html);
        $mpdf->Output('storage/'.$file_name, 'F'); 
        return $file_name; 
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
            $appointment = Appointmnet::find($request->id);
            if( empty($appointment) ){
                return $this->apiOutput("Appointment Data Not Found", 400);
            }
            $this->data = (new AppointmentResource ($appointment))->hide(["intake"]);
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
            $reschedule = false;
            $validator = Validator::make($request->all(), [
                "id"                    => ["required", "exists:appointmnets,id"],
                "therapist_id"  => ["required", "exists:therapists,id"],
                //"ticket_id"  => ["required", "exists:tickets,id"],
                "patient_id"    => ["required", "exists:users,id"],
                "therapist_schedule_id" => ["required", "exists:therapist_schedules,id"],
                "status"        => ["required", "boolean"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $appoinement = Appointmnet::find($request->id);
            if($appoinement->therapist_schedule_id != $request->therapist_schedule_id){
                $reschedule = true;
                $schedule = TherapistSchedule::where("id", $appoinement->therapist_schedule_id)
                    ->update(["status" => "open", "patient_id" => null]);

                $schedule = TherapistSchedule::where("id", $request->therapist_schedule_id)->first();
                if($schedule->status != "open"){
                    return $this->apiOutput("Sorry! This time slot has been booked. You can't book this schedule. please try again.", 400);
                }
                $schedule->status = "booked";
                $schedule->patient_id = $request->patient_id;
                $schedule->save();
            }
           
            $appoinement->updated_by   = $request->user()->id;
            $appoinement->therapist_id = $request->therapist_id;
            $appoinement->patient_id   = $request->patient_id;
            $appoinement->therapist_schedule_id = $request->therapist_schedule_id;
            $appoinement->history      = $request->history ?? null;
            //$appoinement->date         = $schedule->date;
            //$appoinement->start_time   = $schedule->start_time;
            //$appoinement->end_time     = $schedule->end_time;
            $appoinement->fee          = $request->fee;
            $appoinement->language     = $request->language;
            $appoinement->type         = $request->type;
            $appoinement->ticket_id    = $request->ticket_id ?? null;
            $appoinement->trx_type     = $request->trx_type;
            $appoinement->therapist_comment = $request->comment ?? null;
            $appoinement->remarks      = $request->remarks ?? null;
            $appoinement->status       = $request->status;
            if($request->hasFile('picture')){
                $appoinement->image_url = $this->uploadFile($request, 'picture', $this->appointment_uploads, null,null,$appoinement->image_url);
            }
            $appoinement->invoice_url = $this->generatingInvoice($appoinement);
            $appoinement->save();
            $this->saveFileInfo($request, $appoinement);
        
            DB::commit();
            if($reschedule){
                try{
                    event(new Appointment($appoinement, "reschedule"));
                }catch(Exception $e){
    
                }
            }
            $this->apiSuccess("Appointment Updated Successfully");
            $this->data = (new AppointmentResource($appoinement))->hide(["intake"]);
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
            $ticket = Appointmnet::find($request->id);
            $ticket->appointment_ticket_status ="Cancelled";
            $ticket->cancel_appointment_type=$request->cancel_appointment_type;
            $ticket->cancel_reason=$request->cancel_reason;
            $ticket->save();
            $this->apiSuccess("Assigned Ticket Cancelled successfully");
            $this->data = (new AppointmentResource($ticket))->hide(["intake"]);;
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
    public function destroy($id)
    {
        try{
            $data = $this->getModel()->where("id", $id)->delete();
            $this->apiSuccess();
            return $this->apiOutput("Appointment Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    public function deleteFileAppointment(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:appointment_uploads,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $appointmentupload=AppointmentUpload::where('id',$request->id);
            $appointmentupload->delete();
            $this->apiSuccess("Appointment File Deleted successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    public function addFileAppointment(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "appointment_id"            => ["required","exists:appointmnets,id"],

            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $this->saveAddFileInfo($request);
            $this->apiSuccess("Appointment File Added Successfully");
            return $this->apiOutput();
           
           
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Save File Info
     */
    public function saveAddFileInfo($request){

        $file_path = $this->uploadFile($request, 'file', $this->appointment_uploads,720);
        
        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){

                $data = new AppointmentUpload();
                //$data->created_by   = $request->user()->id;
                $data->appointment_id   = $request->appointment_id;
                $data->file_name    = $request->file_name ?? "Appointment Upload";
                $data->file_url     = $path;
                //$data->file_type    = $request->file_type ;
                //$data->status       = $request->status;
                //$data->remarks      = $request->remarks ?? '';
                $data->save();            

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
