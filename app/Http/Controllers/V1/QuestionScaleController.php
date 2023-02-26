<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScaleResource;
use App\Models\Scale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionScaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = ScaleResource::collection(Scale::all());
            $this->apiSuccess("Question and scale Loaded Successfully");
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
            // $validator = Validator::make(
            //     $request->all(),
            //     [
            //         'name' => 'required',
        
            //     ]
            //    );
                
            //    if ($validator->fails()) {
        
            //     $this->apiOutput($this->getValidationError($validator), 200);
            //    }


            $questions = $request->question;

            foreach($questions as $key=>$value){
                // $question[] = $value['question'];
                // $scale[] = $value['scale'];
 
                $data = new Scale();
                $data->patient_id = $request->patient_id;
                $data->question_id = $request->question[$key] ?? null;
                $data->scale = $request->scale[$key] ?? null;
                $data->save();


            }


            // foreach($scale as $scales){
            //     $scale = $scales;
            //     $data = new Scale();
            //     $data->patient_id = $request->patient_id;
            //     // $data->questions = $question;
            //     // $data->question_id = $value['question'];
            //     $data->scale = $scale;
            //     return $data;
            // }
            // foreach($question as $questions){
            //     $question = $questions;
            // }

               
                // $data->save();
            // echo $scale[];
        //     foreach($scale as $scales){
        //     foreach($question as $questions){
        //         // echo $scales;
        //         // echo $questions;
        //         $data = new Scale();
        //         $data->patient_id = $request->patient_id;
        //         $data->question_id = $questions;
        //         $data->scale = $scales;
        //         $data->save();
            
        //  }
        //  }
            // for ($i = 0; $i <= $questions ; $i++) {
            //     foreach ($questions [$keys[$i]] as $key => $value) {
            //         echo $keys[$i] . " : " . $value . "<br>";
            //         // $question = $keys[$i];
            //         // $scale = $value;
            //         // echo $question;
            //     }
            // }
            
            
            
            
            

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
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
