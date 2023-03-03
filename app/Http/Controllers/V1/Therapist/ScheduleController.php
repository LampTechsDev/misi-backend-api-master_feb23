<?php

namespace App\Http\Controllers\V1\Therapist;

use App\Http\Components\Traits\Schedule;
use App\Http\Controllers\Controller;
use App\Http\Resources\TherapistScheduleResource;
use App\Http\Resources\TherapistScheduleSettingsResource;
use App\Models\TherapistSchedule;
use App\Models\TherapistScheduleSettings;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    use Schedule;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $schedule = TherapistSchedule::where("therapist_id", $request->user()->id)->orderBy("date", "ASC")->orderBY("id", "ASC");
            if( !empty($request->date) ){
                $schedule->where("date", ">=", Carbon::parse($request->date)->format("Y-m-d"));
            }else{
                $schedule->where("date", ">=", date("Y-m-d"));
            }
            $schedules = $schedule->get();
            $this->data = TherapistScheduleResource::collection($schedules)->hide(["patient","created_by", "updated_by"]);
            $this->apiSuccess("Therapist Schedules Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try{
            $schedule_settings = TherapistScheduleSettings::where("therapist_id", $request->user()->id)->first();
            $this->data = new TherapistScheduleSettingsResource($schedule_settings);
            $this->apiSuccess("Therapist Schedule Settings Loaded Successfully");
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
        $request->merge(["therapist_id" => $request->user()->id]);
        $days = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];   
        $validator = Validator::make($request->all(),[
            "interval_time" => ["required", "numeric", "min:10"],
            "start_time"    => ["required", "date_format:H:i"],
            "end_time"      => ["required", "date_format:H:i"],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            "holiday"       => ["required", "array", Rule::in($days)],
        ],[
            "holiday.in"    => "Day Name is not Match. use small letter in days name",   
        ]);
            
        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }

        try{
            DB::beginTransaction();
            $settings = $this->addOrUpdateScheduleSettings($request);
            $this->generateSchedule($settings, $request);
            DB::commit();
            $schedules = TherapistSchedule::orderBy("date", "ASC")->orderBY("id", "ASC")
                ->whereBetween("date", [$request->start_date, $request->end_date])
                ->where("therapist_id", $settings->therapist_id)->get();
            
            $this->data = $this->data = TherapistScheduleResource::collection( $schedules);
            $this->apiSuccess("Therapist Schedule Added Successfully");
            return $this->apiOutput();        
        }
        catch(Exception $e){
            DB::rollBack();
            return $this->apiOutput($this->getError( $e), 500);
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
        $validator = Validator::make($request->all(),[
            "id"  => ["required", "exists:therapist_schedules,id"],
        ],[
            "id.required" => "Therapist Schedule ID Required",
        ]); 

        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }

        try{
            $schedule = TherapistSchedule::find($request->id);
            if( empty($schedule) ){
                return $this->apiOutput("Therapist Data Not Found", 400);
            }
            $this->data = (new TherapistScheduleResource ($schedule));
            $this->apiSuccess("Schedule Detail loaded Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
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
        $validator = Validator::make($request->all(),[
            "id"  => ["required", "exists:therapist_schedules,id"],
        ],[
            "id.required" => "Therapist Schedule ID Required",
        ]); 
        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }
        $schedule = TherapistSchedule::where("id", $request->id)
            ->where("therapist_id", $request->user()->id)
            ->first();
        if($schedule->status != "open"){
            return $this->apiOutput("Sorry! You Can't Delete this schedule at this time", 400);
        } 
        $schedule->delete();
        $this->apiSuccess("Schedule Deleted Successfully");
        return $this->apiOutput();
    }

    /**
     * Multiple Schedule Delete
     */
    public function multipleDelete(Request $request){
        $validator = Validator::make($request->all(),[
            "id"    => ["required", "array"],
            "id.*"  => ["exists:therapist_schedules,id"],
        ],[
            "id.required"   => "Therapist Schedule ID Required",
            "id.array"      => "Accept Only Array List",
        ]); 
        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }
        TherapistSchedule::whereIn("id", $request->id)
            ->where("therapist_id", $request->user()->id)
            ->where("status", "open")->delete();

        $this->apiSuccess("Multiple Schedule Deleted Successfully");
        return $this->apiOutput();
    }


    /**
     * Cancel Schedule
     */
    public function cancelTherapistSchedule(Request $request)
    {
        try{    
            $validator = Validator::make( $request->all(),[
                "id"    => ["required", "exists:therapist_schedules,id"]
            ]);

           if ($validator->fails()) {
                $this->apiOutput($this->getValidationError($validator), 200);
           }
            $therapistSchedule = TherapistSchedule::find($request->id);
            $therapistSchedule->cancel_reason = $request->cancel_reason;
            $therapistSchedule->save();
            $this->apiSuccess("Therapist Schedule  cancelled successfully");
            $this->data = (new TherapistScheduleResource($therapistSchedule));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Get All Available Schedule
     */
    public function therapistAvailableSchedule(Request $request){
        $users = DB::table('therapists')
            ->join('therapist_schedules', 'therapist_schedules.therapist_id', '=', 'therapists.id')
            ->where("therapist_schedules.date", ">=", date("Y-m-d"))
            ->where("therapist_schedules.status", '=',"open")
            ->where("therapist_id", $request->user()->id)
            ->select('therapists.id','therapists.first_name','therapists.last_name','therapists.phone','therapists.profile_pic',DB::raw('count(*) as  total') )
            ->groupBy('therapists.id','therapists.first_name','therapists.last_name','therapists.phone','therapists.profile_pic')
            ->get();
            return response()->json($users, 201);                 
    }
}
