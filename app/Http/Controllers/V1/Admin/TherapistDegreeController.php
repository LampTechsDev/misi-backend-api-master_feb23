<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller\V1;
use App\Models\TherapistDegree;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Resources\TherapistDegreeResource;

class TherapistDegreeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TherapistDegreeResource::collection(TherapistDegree::all());
            $this->apiSuccess("Therapist Degree Loaded Successfully");
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
        $validator = Validator::make($request->all(),[
                'name' => ["required","min:2"],
            ]);
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 400);
           }
   
            $degree = new TherapistDegree();
            $degree->therapist_id = $request->therapist_id;
            $degree->degree_id = $request->degree_id;
            $degree->created_by = $request->user()->id ?? null;
            // $degree->created_at = Carbon::Now();
            $degree->save();
            $this->apiSuccess();
            $this->data = (new TherapistDegreeResource($degree));
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
            $therapistdegree = TherapistDegree::find($request->id);
            if( empty($therapistdegree) ){
                return $this->apiOutput("Therapist Degree Schedule Not Found", 400);
            }
            $this->data = (new TherapistDegreeResource ($therapistdegree));
            $this->apiSuccess("Therapist Degree Schedule Show Successfully");
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2',
                'remarks' => 'nullable|min:4'
    
            ]
           );
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 400);
           }
   
            $degree = TherapistDegree::find($id);
            $degree->therapist_id = $request->therapist_id;
            $degree->degree_id = $request->degree_id;
            $degree->updated_by = $request->user()->id ?? null;
            // $degree->updated_at = Carbon::Now();
            $degree->save();
            $this->apiSuccess();
            $this->data = (new TherapistDegreeResource($degree));
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
        TherapistDegree::destroy($id);
        $this->apiSuccess();
        return $this->apiOutput("Therapist Degree Deleted Successfully", 200);
    }
}
