<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

use App\Http\Resources\PibFormulaResource;
use App\Models\PibFormula;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PibFormulaController extends Controller
{

    /**
     * Get Current Table Model
     */
    private function getModel(){
        return new PibFormula();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $pib = PibFormula::all();
            $this->data = PibFormulaResource::collection($pib);
            $this->apiSuccess("PIB Loaded Successfully");
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
                'patient_id'            => ['nullable', "exists:users,id"],
                'ticket_id'             => ['nullable', "exists:tickets,id"],
                'name'                  => ['required', "string", "min:2"],
                "type_of_legitimation"  => ["nullable", "string"],
                "document_number"       => ["nullable", "string"],
                "identify_expire_date"  => ["nullable", "date"],
                "status"                => ["required", Rule::in(["active", "inactive", "pending", "cancel"])],
                "remarks"               => ["required", "string"]
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $data = $this->getModel();

            $data->patient_id           = $request->patient_id;
            $data->ticket_id            = $request->ticket_id;
            $data->name                 = $request->name;
            $data->type_of_legitimation = $request->type_of_legitimation;
            $data->document_number      = $request->document_number;
            $data->identify_expire_date = $request->identify_expire_date;
            $data->created_by            = $request->user()->id;
            $data->status                = $request->status;
            $data->remarks               = $request->remarks;
            $data->save();

            DB::commit();
            $this->apiSuccess("PIB Formula Added Successfully");
            $this->data = (new PibFormulaResource($data));
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
            $data = PibFormula::find($request->id);
            if( empty($data) ){
                return $this->apiOutput("Pib Formula Not Found", 400);
            }
            $this->data = (new PibFormulaResource ($data));
            $this->apiSuccess("PIB Detail Show Successfully");
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
                "id"                    => ["required", "exists:pib_formulas,id"],
                'patient_id'            => ['nullable', "exists:users,id"],
                'ticket_id'             => ['nullable', "exists:tickets,id"],
                'name'                  => ['required', "string", "min:2"],
                "type_of_legitimation"  => ["nullable", "string"],
                "document_number"       => ["nullable", "string"],
                "identify_expire_date"  => ["nullable", "date"],
                "status"                => ["required", Rule::in(["active", "inactive", "pending", "cancel"])],
                "remarks"               => ["required", "string"]
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
        
            DB::beginTransaction();
            $data = PibFormula::find($request->id);

            $data->patient_id           = $request->patient_id;
            $data->ticket_id            = $request->ticket_id;
            $data->name                 = $request->name;
            $data->type_of_legitimation = $request->type_of_legitimation;
            $data->document_number      = $request->document_number;
            $data->identify_expire_date = $request->identify_expire_date;
            $data->updated_by           = $request->user()->id;
            $data->status               = $request->status;
            $data->remarks              = $request->remarks;
            $data->save();
            
            DB::commit();
            $this->apiSuccess("PIB Formula Updated Successfully");
            $this->data = (new PibFormulaResource($data));
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
            return $this->apiOutput("PIB Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    public function pibshowPatientTicket(Request $request)
    {
        try{
            
            $validator = Validator::make( $request->all(),[
                //"type"            => ["required"],
               
            ]);
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            $pibdata = PibFormula::where("patient_id",$request->patient_id)
                                 ->orWhere("ticket_id",$request->ticket_id)
                                 ->get();
            
            $this->data = PibFormulaResource::collection($pibdata);
            $this->apiSuccess("Question Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

}
