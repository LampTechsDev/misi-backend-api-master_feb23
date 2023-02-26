<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceCategoryResource;
use App\Http\Resources\TherapistResource;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class ServiceCategoryController extends Controller
{
    //Get All Value
    public function index(){
        try{
            $this->data = ServiceCategoryResource::collection(ServiceCategory::all());
            $this->apiSuccess("Service Category Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    public function store(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:4',
                'remarks' => 'nullable|min:4' 
            ]);
             
            if ($validator->fails()) {    
                $this->apiOutput($this->getValidationError($validator), 400);
            }
    
            $service = new ServiceCategory();
            $service->name = $request->name;
            $service->status = $request->status;
            $service->remarks = $request->remarks ?? "";
            $service->created_by = $request->user()->id ?? null;
            $service->save();
            
            $this->apiSuccess();
            $this->data = (new ServiceCategoryResource($service));
            return $this->apiOutput("Service Category added successfully", 201);

        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    public function show(Request $request)
    {
        //  $temp=ServiceCategory::find($id);
        //  return $temp;
        try{
            $servicecategory = ServiceCategory::find($request->id);
            if( empty($servicecategory) ){
                return $this->apiOutput("Therapist Data Not Found", 400);
            }
            $this->data = (new ServiceCategoryResource ($servicecategory));
            $this->apiSuccess("Therapist Detail Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

     //Update Service
     public function update(Request $request){

        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:4',
                'remarks' => 'nullable|min:4' 
            ]);
             
            if ($validator->fails()) {    
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
    
            $service = ServiceCategory::find($request->id);
            if( empty($service) ){
                return $this->apiOutput("Service Category Not Found", 400);
            }
            $service->name = $request->name;
            $service->status = $request->status;
            $service->remarks = $request->remarks ?? "";
            $service->updated_by = $request->user()->id ?? null;
            $service->save();
            
            $this->apiSuccess();
            $this->data = (new ServiceCategoryResource($service));
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
     }

     //Service Destroy
    public function destroy($id){
        try{
            ServiceCategory::destroy($id);
            $this->apiSuccess();
            return $this->apiOutput("Service Category Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}
