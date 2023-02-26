<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceSubCategory;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ServiceSubCategoryResource;
use App\Http\Resources\ServiceSubCategoryCollection;
use Exception;
use Illuminate\Support\Facades\DB;

class ServiceSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            
            $this->data = ServiceSubCategoryResource::collection(ServiceSubCategory::all());
            $this->apiSuccess("Service SubCategory Loaded Successfully");
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
                'remarks' => 'nullable|min:4' 
            ]);
             
            if ($validator->fails()) {
    
                $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $subservice = new ServiceSubCategory();
            $subservice->service_categorie_id = $request->service_categorie_id;
            $subservice->name = $request->name;
            $subservice->status = $request->status;
            $subservice->remarks = $request->remarks ?? "";
            $subservice->created_by = $request->user()->id ?? null;
            $subservice->save();
            
            $this->apiSuccess();
            $this->data = (new ServiceSubCategoryResource($subservice));
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
    //    $temp=ServiceCategory::all();
    //    return $temp;

       try{
        $servicesubcategory = ServiceSubCategory::find($request->id);
        if( empty($servicesubcategory) ){
            return $this->apiOutput("Sub Service Category Not Found", 400);
        }
        $this->data = (new ServiceSubCategoryResource ($servicesubcategory));
        $this->apiSuccess("Sub Service Category Show Successfully");
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
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:4',
                'remarks' => 'nullable|min:4' 
            ]);
             
            if ($validator->fails()) {
    
                $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $subservice = ServiceSubCategory::find($id);
            $subservice->service_categorie_id = $request->service_categorie_id;
            $subservice->name = $request->name;
            $subservice->status = $request->status;
            $subservice->remarks = $request->remarks ?? "";
            $subservice->updated_by = $request->user()->id ?? null;
            $subservice->save();
            
            $this->apiSuccess();
            $this->data = (new ServiceSubCategoryResource($subservice));
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
        ServiceSubCategory::destroy($id);
        $this->apiSuccess();
        return $this->apiOutput("Service SubCategory Deleted Successfully", 200);
    }
}
