<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TherapistType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Resources\TherapistTypeResource;

class TherapistTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TherapistTypeResource::collection(TherapistType::all());
            $this->apiSuccess("Therapist Type Loaded Successfully");
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
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:4',
                'remarks' => 'nullable|min:4'
    
            ]
           );
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 200);
           }
   
            $therapist_type = new TherapistType();
            $therapist_type->name = $request->name;
            $therapist_type->status = $request->status;
            $therapist_type->created_by = $request->user()->id ?? null;
            // $therapist_type->created_at = Carbon::Now();
            $therapist_type->save();
            $this->apiSuccess();
            $this->data = (new TherapistTypeResource($therapist_type));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
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
                'name' => 'required|min:4',
                'remarks' => 'nullable|min:4'
    
            ]
           );
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 200);
           }
   
            $therapist_type = TherapistType::find($id);
            $therapist_type->name = $request->name;
            $therapist_type->status = $request->status;
            $therapist_type->updated_by = $request->user()->id ?? null;
            // $therapist_type->updated_at = Carbon::Now();
            $therapist_type->save();
            $this->apiSuccess();
            $this->data = (new TherapistTypeResource($therapist_type));
            return $this->apiOutput();
        }
        catch(Exception $e){
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
        TherapistType::destroy($id);
        $this->apiSuccess();
        return $this->apiOutput("Therapist Type Deleted Successfully", 200);
    }
}
