<?php

namespace App\Http\Controllers\V1\Admin;

use App\Events\AccountRegistration;
use App\Events\PasswordReset as PasswordResetEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Therapist;
use App\Models\TherapistUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Resources\TherapistResource;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TherapistController extends Controller
{
    /**
     * Get Current Table Model
     */
    private function getModel(){
        return new Therapist();
    }

    /**
     * Show Login
     */
    public function showLogin(Request $request){
        $this->data = [
            "email"     => "required",
            "password"  => "required",
        ];
        $this->apiSuccess("This credentials are required for Login ");
        return $this->apiOutput();
    }

    /**
     * Login
     */
    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email", "exists:therapists,email"],
                "password"  => ["required", "string", "min:4", "max:40"]
            ]); 
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $therapist = $this->getModel()->where("email", $request->email)->first();
            if( !Hash::check($request->password, $therapist->password) ){
                return $this->apiOutput("Sorry! Password Dosen't Match", 401);
            }
            if( !$therapist->status ){
                return $this->apiOutput("Sorry! your account is temporaly blocked", 401);
            }
            // Issueing Access Token
            $this->access_token = $therapist->createToken($request->ip() ?? "therapist_access_token")->plainTextToken;
            $this->apiSuccess("Login Successfully");
            $this->data = (new TherapistResource($therapist));
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        $this->apiSuccess("Logout Successfully");
        return $this->apiOutput();
   
    }

    /**
     * Forget Password
     */
    public function forgetPassword(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "exists:therapists,email"],
            ],[
                "email.exists"  => "No Record found under this email",
            ]);

            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $user = Therapist::where("email", $request->email)->first();
            $password_reset = PasswordReset::where("tableable", $user->getMorphClass())
                ->where("tableable_id", $user->id)->where("is_used", false)
                ->where("expire_at", ">=", now()->format('Y-m-d H:i:s'))
                ->orderBy("id", "DESC")->first();
            if( empty($password_reset) ){
                $token = rand(111111, 999999);
                $password_reset = new PasswordReset();
                $password_reset->tableable      = $user->getMorphClass();
                $password_reset->tableable_id   = $user->id;
                $password_reset->email          = $user->email;
                $password_reset->token          = $token;
            }   
            $password_reset->expire_at      = now()->addHour();
            $password_reset->save();

            // Send Password Reset Email
            event(new PasswordResetEvent($password_reset));
            
            $this->apiSuccess("Password Reset Code sent to your registared Email.");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    } 
    
    /**
     * Password Reset
     */
    public function passwordReset(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "exists:therapists,email"],
                "code"      => ["required", "exists:password_resets,token"],
                "password"  => ["required", "string"],
            ],[
                "email.exists"  => "No Record found under this email",
                "code.exists"   => "Invalid Verification Code",
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $password_reset = PasswordReset::where("email", $request->email)
                ->where("is_used", false)
                ->where("expire_at", ">=", now()->format('Y-m-d H:i:s'))
                ->first();
            if( empty($password_reset) ){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $password_reset->is_used = true;
            $password_reset->save();

            $user = $password_reset->user;
            $user->password = bcrypt($request->password);
            $user->save();

            DB::commit();
            try{
                event(new PasswordResetEvent($password_reset, true));
            }catch(Exception $e){

            }
            $this->apiSuccess("Password Reset Successfully.");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $therapists = Therapist::all();
            $this->data = TherapistResource::collection($therapists);
            $this->apiSuccess("Therapist Loaded Successfully");
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
      
        $validator = Validator::make($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required',
            "email"     => ["required", "email", "unique:therapists"],
            "status"        => ["required", "boolean"],
            "phone"     => ["required", "numeric", "unique:therapists"]
        ]);
            
        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }

        try{
            DB::beginTransaction();

            $data = $this->getModel();
            $data->created_by = $request->user()->id;
            $data->first_name = $request->first_name;                  
            $data->last_name = $request->last_name;         
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->address = $request->address;
            $data->language = $request->language;
            $data->bsn_number = $request->bsn_number;
            $data->dob_number = $request->dob_number;
            $data->insurance_number = $request->insurance_number;
            $data->emergency_contact = $request->emergency_contact ?? 0;
            $data->gender = $request->gender;
            //$data->date_of_birth = /*$request->date_of_birth*/ Carbon::now();
            $data->date_of_birth = $request->date_of_birth;
            $data->status = $request->status;
            $data->therapist_type_id = $request->therapist_type_id;
            $data->blood_group_id = $request->blood_group_id;
            $data->state_id = $request->state_id;
            $data->country_id = $request->country_id;
            $data->password = bcrypt($request->password);
            if($request->hasFile('picture')){
                $data->profile_pic = $this->uploadFile($request, 'picture', $this->therapist_uploads, null,null,$data->profile_pic);
            }
            
            $data->save();
            $this->saveFileInfo($request, $data);
            DB::commit();
            try{
                event(new AccountRegistration($data, "therapist"));
            }catch(Exception $e){

            }
            $this->apiSuccess("Therapist Info Added Successfully");
            $this->data = (new TherapistResource($data));
            return $this->apiOutput();        
        }
        catch(Exception $e){
            DB::rollBack();
            return $this->apiOutput($this->getError( $e), 500);
        }                
    }

    // Save File Info
    public function saveFileInfo($request, $therapist){
        $file_path = $this->uploadFile($request, 'file', $this->therapist_uploads, 720);
  
        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){
            $data = new TherapistUpload();
            $data->therapist_id = $therapist->id;
            $data->file_name    = $request->file_name ?? "Therapist Upload";
            $data->file_url     = $path;
            $data->save();
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
            $therapist = Therapist::find($request->id);
            if( empty($therapist) ){
                return $this->apiOutput("Therapist Data Not Found", 400);
            }
            $this->data = (new TherapistResource ($therapist));
            $this->apiSuccess("Therapist Detail Show Successfully");
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
            "id"        => ['required', "exists:therapists,id"],
            'first_name' =>['required', "string", "min:2"],
            'last_name' => ['nullable', "string", "min:2"],
            "email"     => ["required", "email", Rule::unique('therapists', 'email')->ignore($request->id)],
            "phone"     => ["required", "numeric", Rule::unique('therapists', 'phone')->ignore($request->id)],
            "address"   => ["nullable", "string"],
            "language"   => ["nullable", "string"],
            "bsn_number" => ["nullable", "string"],
            "dob_number" => ["nullable", "string"],
            "gender"     => ["required", "string"],
            "status"        => ["required", "boolean"],
            "profile_pic"=> ["nullable", "file"],
        ]);
        
        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }
            DB::beginTransaction();
            //$data = $this->getModel()->find($request->id);
            $data = Therapist::find($request->id);
            $data->updated_by = $request->user()->id ?? null;
            
            $data->first_name = $request->first_name;                  
            $data->last_name = $request->last_name;         
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->address = $request->address;
            $data->language = $request->language;
            $data->bsn_number = $request->bsn_number;
            $data->dob_number = $request->dob_number;
            $data->insurance_number = $request->insurance_number;
            $data->emergency_contact = $request->emergency_contact ?? 0;
            $data->gender = $request->gender;
            $data->date_of_birth = $request->date_of_birth;
            $data->status = $request->status;
            $data->therapist_type_id = $request->therapist_type_id;
            $data->blood_group_id = $request->blood_group_id;
            $data->state_id = $request->state_id;
            $data->country_id = $request->country_id;
            //$data->password = bcrypt($request->password);
            if($request->hasFile('picture')){
                $data->profile_pic =  $this->uploadFile($request, "picture", $this->therapist_uploads, "150", null, $data->profile_pic);
            }
           
            $data->save();
            $this->updateFileInfo($request, $data->id);

            DB::commit();            
            $this->apiSuccess("Therapist Info Updated Successfully");
            $this->data = (new TherapistResource($data))->hide(["updated_by", "created_by"]);
            return $this->apiOutput(); 
        }
        catch(Exception $e){
            DB::rollBack();
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

     //Update File Info
    public function updateFileInfo($request, $id){
        $upload_files = $this->uploadFile($request, 'file', $this->therapist_uploads);
        if( is_array($upload_files) ){
            foreach($upload_files as $file){
                $upload = new TherapistUpload();
                $upload->therapist_id = $id;
                $upload->file_name    = $request->file_name ?? "Therapist Upload Updated";
                $upload->file_url     = $file;
                $upload->save();    
            }
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
            $data = $this->getModel()->find($id);
            TherapistUpload::where('therapist_id',$data->id)->delete();
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("Therapist Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
  
    public function deleteFileTherapist(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:therapist_uploads,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $therapistupload=TherapistUpload::where('id',$request->id);
            $therapistupload->delete();
            $this->apiSuccess("Therapist File Deleted successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Therapist Profile
     */
    public function getProfile(Request $request){
        try{
            $therapist = $request->user();
            $this->data["therapist"] = (new TherapistResource($therapist))->hide(["created_by", "updated_by"]);
            $this->apiSuccess();
            return $this->apiOutput("Therapist Profile loaded Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    public function addFileTherapist(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "therapist_id"            => ["required","exists:therapists,id"],

            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $this->saveAddFileInfo($request);
            $this->apiSuccess("Therapist File Added Successfully");
            return $this->apiOutput();
           
           
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Save File Info
     */
    public function saveAddFileInfo($request){

        $file_path = $this->uploadFile($request, 'file', $this->therapist_uploads,720);

        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){

                $data = new TherapistUpload();
                //$data->created_by   = $request->user()->id;
                $data->therapist_id   = $request->therapist_id;
                $data->file_name    = $request->file_name ?? "Therapist Upload";
                $data->file_url     = $path;
                //$data->file_type    = $request->file_type ;
                //$data->status       = $request->status;
                //$data->remarks      = $request->remarks ?? '';
                $data->save();            

            }
      
    }

    public function updateTherapistFileInfo(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:therapist_uploads,id"],

            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $data = TherapistUpload::find($request->id);
            
            if($request->hasFile('picture')){
                $data->file_url = $this->uploadFile($request, 'picture', $this->therapist_uploads, null,null,$data->file_url);
            }

            $data->save();
          
            $this->apiSuccess("Therapist File Updated Successfully");
            return $this->apiOutput();
           
           
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}
