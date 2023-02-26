<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = QuestionResource::collection(Question::all());
            $this->apiSuccess("Question Loaded Successfully");
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
                    'question' => 'required',
        
                ]
               );
                
               if ($validator->fails()) {
        
                $this->apiOutput($this->getValidationError($validator), 200);
               }
       
                $question = new Question();
                $question->question = $request->question ?? null;
                $question->type = $request->type ?? null;
                $question->created_by = $request->user()->id ?? null;
                $question->save();
                $this->apiSuccess();
                $this->data = (new QuestionResource($question));
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
            
            $validator = Validator::make( $request->all(),[
                "type"            => ["required"],
               
            ]);
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            $question = Question::where("type",$request->type)->get();

            $this->data = QuestionResource::collection($question);
            $this->apiSuccess("Question Loaded Successfully");
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
                    'question' => 'required',
        
                ]
               );
                
               if ($validator->fails()) {
        
                $this->apiOutput($this->getValidationError($validator), 200);
               }
       
                $question = Question::find($id);
                $question->question = $request->question ?? null;
                $question->type = $request->type ?? null;
                $question->created_by = $request->user()->id ?? null;
                $question->save();
                $this->apiSuccess("Question Updated Successfull");
                $this->data = (new QuestionResource($question));
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
        Question::destroy($id);
        $this->apiSuccess();
        return $this->apiOutput("Question Deleted Successfully", 200);
    }
    
}
