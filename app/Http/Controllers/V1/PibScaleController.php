<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PibScaleResource;
use App\Models\PibScale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PibScaleController extends Controller
{


     /**
     * Get Current Table Model
     */
    private function getModel(){
        return new PibScale();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $pib_scale = PibScale::all();
            $this->data = PibScaleResource::collection($pib_scale);
            $this->apiSuccess("PIB Scale Loaded Successfully");
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
                'patient_id'            => ['required', "exists:users,id"],
                'pib_formula_id'        => ['required', "exists:pib_formulas,id"],
                'question_id'           => ['nullable', "exists:questions,id"],
                "scale_value"           => ["required", "numeric", "min:0", "max:100"],
                "status"                => ["nullable", Rule::in(["active", "inactive", "pending", "cancel"])],
                "remarks"               => ["required", "string"]
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $data = $this->getModel();
            $data->patient_id       = $request->patient_id;
            $data->pib_formula_id   = $request->pib_formula_id;
            $data->question_id      = $request->question_id;
            $data->scale_value      = $request->scale_value;
            $data->status           = $request->status ?? "active";
            $data->remarks          = $request->remarks;
            $data->created_by       = $request->user()->id;
            $data->save();

            DB::commit();
            $this->apiSuccess("PIB Scale Added Successfully");
            $this->data = (new PibScaleResource($data));
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
        try{
            $data = PibScale::find($request->id);
            if( empty($data) ){
                return $this->apiOutput("Pib Scale Not Found", 400);
            }
            $this->data = (new PibScaleResource($data));
            $this->apiSuccess("PIB Scale Detail Show Successfully");
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
            $validator = Validator::make($request->all(),[
                "id"                    => ["required", "exists:pib_scales,id"],
                'patient_id'            => ['required', "exists:users,id"],
                'pib_formula_id'        => ['required', "exists:pib_formulas,id"],
                'question_id'           => ['nullable', "exists:questions,id"],
                "scale_value"           => ["required", "numeric", "min:0", "max:100"],
                "status"                => ["nullable", Rule::in(["active", "inactive", "pending", "cancel"])],
                "remarks"               => ["required", "string"]
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $data = PibScale::find($request->id);

            $data->patient_id       = $request->patient_id;
            $data->pib_formula_id   = $request->pib_formula_id;
            $data->question_id      = $request->question_id;
            $data->scale_value      = $request->scale_value;
            $data->status           = $request->status ?? "active";
            $data->remarks          = $request->remarks;
            $data->updated_by       = $request->user()->id;
            $data->save();
            DB::commit();

            $this->apiSuccess("PIB Scale Updated Successfully");
            $this->data = (new PibScaleResource($data));
            return $this->apiOutput();
        }
        catch(Exception $e){
            DB::rollBack();
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
            $data = $this->getModel()->find($request->id);
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("PIB Scale Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    public function pibScaleshowPatient(Request $request)
    {
        try{
            
            $validator = Validator::make( $request->all(),[
                'patient_id'            => ['required', "exists:users,id"],
               
            ]);
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            $pibscaledata = PibScale::where("patient_id",$request->patient_id)->get();  
            $this->data = PibScaleResource::collection($pibscaledata);
            $this->apiSuccess("Question Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
}
