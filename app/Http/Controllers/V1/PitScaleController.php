<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PitScaleResource;
use App\Models\PitScale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class PitScaleController extends Controller
{


     /**
     * Get Current Table Model
     */
    private function getModel(){
        return new PitScale();
    }


   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $pit_scale = PitScale::all();
            $this->data = PitScaleResource::collection($pit_scale);
            $this->apiSuccess("PIT Scale Loaded Successfully");
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
                'pit_formula_id'        => ['required', "exists:pit_formulas,id"],
                'question_id'           => ['nullable', "exists:questions,id"],
                "scale_value"           => ["required", "numeric", "min:0", "max:100"],
                "status"                => ["nullable", Rule::in(["active", "inactive", "pending", "cancel"])],
                "remarks"               => ["nullable", "string"]
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $data = $this->getModel();
            $data->patient_id       = $request->patient_id;
            $data->pit_formula_id   = $request->pit_formula_id;
            $data->question_id      = $request->question_id;
            $data->scale_value      = $request->scale_value;
            $data->status           = $request->status ?? "active";
            $data->remarks          = $request->remarks;
            $data->created_by       = $request->user()->id;
            $data->save();

            DB::commit();
            $this->apiSuccess("PIT Scale Added Successfully");
            $this->data = (new PitScaleResource($data));
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
            $data = PitScale::find($request->id);
            if( empty($data) ){
                return $this->apiOutput("Pit Scale Not Found", 400);
            }
            $this->data = (new PitScaleResource($data));
            $this->apiSuccess("PIT Scale Detail Show Successfully");
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
                "id"                    => ["required", "exists:pit_scales,id"],
                'patient_id'            => ['required', "exists:users,id"],
                'pit_formula_id'        => ['required', "exists:pit_formulas,id"],
                'question_id'           => ['nullable', "exists:questions,id"],
                "scale_value"           => ["required", "numeric", "min:0", "max:100"],
                "status"                => ["nullable", Rule::in(["active", "inactive", "pending", "cancel"])],
                "remarks"               => ["nullable", "string"]
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $data = PitScale::find($request->id);

            $data->patient_id       = $request->patient_id;
            $data->pit_formula_id   = $request->pit_formula_id;
            $data->question_id      = $request->question_id;
            $data->scale_value      = $request->scale_value;
            $data->status           = $request->status ?? "active";
            $data->remarks          = $request->remarks;
            $data->updated_by       = $request->user()->id;
            $data->save();
            DB::commit();

            $this->apiSuccess("PIT Scale Updated Successfully");
            $this->data = (new PitScaleResource($data));
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
            $data = $this->getModel()->where("id", $request->id)->delete();
            $this->apiSuccess();
            return $this->apiOutput("PIT Scale Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    public function pitScaleshowPatient(Request $request)
    {
        try{
            
            $validator = Validator::make( $request->all(),[
                'patient_id'            => ['required', "exists:users,id"],
               
            ]);
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            $pitscaledata = PitScale::where("patient_id",$request->patient_id)->get();  
            $this->data = PitScaleResource::collection($pitscaledata);
            $this->apiSuccess("Question Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
}
