<?php

namespace App\Http\Controllers\V1\Admin;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TherapistService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TherapistServiceResource;

class TherapistServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email", "exists:therapists,email"],
                "password"  => ["required", "string", "min:4", "max:40"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $therapistservice = $this->getModel()->where("email", $request->email)->first();
            if( !Hash::check($request->password, $therapistservice->password) ){
                return $this->apiOutput("Sorry! Password Dosen't Match", 401);
            }
            if( !$therapistservice->status ){
                return $this->apiOutput("Sorry! your account is temporaly blocked", 401);
            }
            // Issueing Access Token
            $this->access_token = $therapistservice->createToken($request->ip() ?? "therapist_access_token")->plainTextToken;
            $this->apiSuccess("Login Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }


    }
    public function index()
    {
        try{
            $this->data = TherapistServiceResource::collection(TherapistService::all());
            $this->apiSuccess("Therapist Service Loaded Successfully");
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
    public function create()
    {
        //
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
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:4',

            ]
           );

           if ($validator->fails()) {

            $this->apiOutput($this->getValidationError($validator), 400);
           }

            $therapistservice = new TherapistService();
            $therapistservice->therapist_id  = $request->therapist_id ;
            $therapistservice->name = $request->name;
            $therapistservice->status = $request->status;
            $therapistservice->service_category_id  = $request->service_category_id ;
            $therapistservice->service_sub_category_id  = $request->service_sub_category_id ;
            $therapistservice->created_by = $request->user()->id ?? null;
            // $therapistservice->created_at = Carbon::Now();
            $therapistservice->save();
            $this->apiSuccess();
            $this->data = (new TherapistServiceResource($therapistservice));
            return $this->apiOutput();
        }catch(Exception $e){
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
        //return 10;
        try{
            $therapistservice = TherapistService::find($request->id);
            //return $therapistservice;
            if( empty($therapistservice) ){
                return $this->apiOutput("Ticket Data Not Found", 400);
            }
            $this->data = (new TicketResource($therapistservice));
            $this->apiSuccess("Therapist Service Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit(Request $request)
    // {
    //     try{
    //         $therapistservice = TherapistService::find($request->id);
    //         if( empty($therapistservice) ){
    //             return $this->apiOutput("Ticket Data Not Found", 400);
    //         }
    //         $this->data = (new TicketResource($therapistservice));
    //         $this->apiSuccess("Therapist Service Info Show Successfully");
    //         return $this->apiOutput();
    //     }catch(Exception $e){
    //         return $this->apiOutput($this->getError($e), 500);
    //     }
    // }

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
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:4',
                'remarks' => 'nullable|min:4'

            ]
           );

           if ($validator->fails()) {

            $this->apiOutput($this->getValidationError($validator), 400);
           }

            $therapistservice = TherapistService::find($request->id);
            $therapistservice->name = $request->name;
            $therapistservice->status = $request->status;
            $therapistservice->service_category_id  = $request->service_category_id ;
            $therapistservice->service_sub_category_id  = $request->service_sub_category_id ;
            $therapistservice->updated_by = $request->user()->id ?? null;
            // $therapistservice->updated_at = Carbon::Now();
            $therapistservice->save();
            $this->apiSuccess();
            $this->data = (new TherapistServiceResource($therapistservice));
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
        TherapistService::destroy($id);
        $this->apiSuccess();
        return $this->apiOutput("Therapist Service Deleted Successfully", 200);
    }
}
