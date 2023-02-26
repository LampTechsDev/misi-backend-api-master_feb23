<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\IntakeResource;
use App\Models\AppointmentIntake;
use App\Models\Appointmnet;
use App\Models\Intake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class AppointmentIntakeController extends Controller
{
    public function index(){
        try{
            $appointments = Appointmnet::join("appointment_intakes as ai", "ai.appointment_id", "=", "appointmnets.id")
                ->select("appointmnets.*")->groupBy("appointmnets.id")->get();
            $this->data= AppointmentResource::collection($appointments)->hide(["upload_files", "patient_info", "therapist_info", "therapist_schedule", "ticket"]);
            $this->apiSuccess("Appointment Intake Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    public function store(Request $request)
    {
       
        try{
            $validator = Validator::make($request->all(), [
                "appointment_id" => ["required", "exists:appointmnets,id"],
            ]);

           if ($validator->fails()) {

            $this->apiOutput($this->getValidationError($validator), 200);
           }

            $intake = new AppointmentIntake();
            $intake->appointment_id = $request->appointment_id;
            $intake->intake_date = $request->intake_date;
            $intake->intake_number=$request->intake_number;
            $intake->save();
            $this->apiSuccess();
            $this->data = (new IntakeResource($intake));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    public function show(Request $request)
    {
        try{
            $intakeshow = AppointmentIntake::find($request->id);
            if( empty($intakeshow) ){
                return $this->apiOutput("Appointment Data Not Found", 400);
            }
            $this->data = (new IntakeResource ($intakeshow));
            $this->apiSuccess("Intake Detail Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    public function update(Request $request)
    {
        try{
        $validator = Validator::make(
            $request->all(),
            [
                "appointment_id" => ["required", "exists:appointmnets,id"],

            ]
           );

           if ($validator->fails()) {
            $this->apiOutput($this->getValidationError($validator), 200);
           }

            $intake = AppointmentIntake::find($request->id);
            $intake->appointment_id = $request->appointment_id;
            $intake->intake_date = $request->intake_date;
            $intake->intake_number=$request->intake_number;
            $intake->save();
            $this->apiSuccess();
            $this->data = (new IntakeResource($intake));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);

        }
    }


    public function destroy(Request $request)
    {
        try{
            AppointmentIntake::where("id", $request->id)->delete();
            $this->apiSuccess();
            return $this->apiOutput("Appointment Intake Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    
}
