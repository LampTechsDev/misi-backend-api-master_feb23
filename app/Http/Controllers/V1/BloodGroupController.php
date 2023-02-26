<?php

namespace App\Http\Controllers\V1;

use Exception;
use Carbon\Carbon;
use App\Models\BloodGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BloodGroupResource;

class BloodGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return 10;
        // $temp=BloodGroup::all();
        // return $temp;
        try{
            $this->data = BloodGroupResource::collection(BloodGroup::all());
            $this->apiSuccess("Blood Group Loaded Successfully");
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
                'name' => 'required|min:4',
            ]);

           if ($validator->fails()) {

            $this->apiOutput($this->getValidationError($validator), 200);
           }

            $blood_group = new BloodGroup();
            $blood_group->name = $request->name;
            $blood_group->status = $request->status;
            $blood_group->created_by = $request->user()->id ?? null;
            // $blood_group->created_at = Carbon::Now();
            $blood_group->save();
            $this->apiSuccess();
            $this->data = (new BloodGroupResource($blood_group));
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
        try{
            $bloodgroup = BloodGroup::find($request->id);
            if( empty($bloodgroup) ){
                return $this->apiOutput("Appointment Data Not Found", 400);
            }
            $this->data = (new BloodGroupResource ($bloodgroup));
            $this->apiSuccess("BloodGroup Detail Show Successfully");
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
    public function update(Request $request, $id)
    {
        try{
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:1',

            ]
           );

           if ($validator->fails()) {
            $this->apiOutput($this->getValidationError($validator), 200);
           }

            $blood_group = BloodGroup::find($id);
            $blood_group->name = $request->name;
            $blood_group->status = $request->status;
            $blood_group->updated_by = $request->user()->id ?? null;
            // $blood_group->updated_at = Carbon::Now();
            $blood_group->save();
            $this->apiSuccess();
            $this->data = (new BloodGroupResource($blood_group));
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
            BloodGroup::where("id", $id)->delete();
            $this->apiSuccess();
            return $this->apiOutput("Blood Group Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
}
