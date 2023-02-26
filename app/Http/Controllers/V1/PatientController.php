<?php

namespace App\Http\Controllers\V1;

use App\Events\AccountRegistration;
use App\Events\PasswordReset as PasswordResetEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\PatientUploadResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PatientUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Exception;
use App\Http\Resources\UserResource;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;

class PatientController extends Controller
{

    /**
     * Get Current Table Model
     */
    private function getModel(){
        return new User();
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
        $patient=User::all();
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email", "exists:users,email"],
                "password"  => ["required", "string", "min:4", "max:40"]
            ]); 
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $patient = $this->getModel()->where("email", $request->email)->first();
            if( !Hash::check($request->password, $patient->password) ){
                return $this->apiOutput("Sorry! Password Dosen't Match", 401);
            }
            // if( !$patient->status ){
            //     return $this->apiOutput("Sorry! your account is temporaly blocked", 401);
            // }
            // Issueing Access Token
             //$this->access_token = $admin->createToken($request->ip() ?? "admin_access_token")->plainTextToken;
           
            // $this->access_token = $patient->createToken($request->ip() ?? "patient_access_token")->plainTextToken;
            // Session::put('access_token',$this->access_token);
            $this->access_token = $patient->createToken($request->ip() ?? "patient_access_token")->plainTextToken;
            Session::put('access_token',$this->access_token);
            $this->apiSuccess("Login Successfully");
            $this->data = (new UserResource($patient));
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    public function logout(Request $request){
        
        $user = $request->user();
        $user->tokens()->delete();
        $this->apiSuccess("Logout Successfull");
        return $this->apiOutput();
   
    }

    /**
     * Forget Password
     */
    public function forgetPassword(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "exists:users,email"],
            ],[
                "email.exists"  => "No Record found under this email",
            ]);

            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $user = User::where("email", $request->email)->first();
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
                "email"     => ["required", "exists:users,email"],
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

            try{
                event(new PasswordResetEvent($password_reset, true));
            }catch(Exception $e){

            }

            DB::commit();
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
            $this->data = UserResource::collection(User::all());
            $this->apiSuccess("Patient Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    
     public function missingInfoPatient()
    {
        try{
        $users=User::whereNull('city')
               ->orWhereNull('occupation')
               ->orWhereNull('age')
               ->orWhereNull('emergency_contact')
               ->get();
            $this->data = UserResource::collection($users);
            $this->apiSuccess("Patient MissingInfo Loaded Successfully");
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
        //return 10;
        try{
        $validator = Validator::make(
            $request->all(),
            [ 
                'first_name' => 'required',
                'last_name' => 'required',
                "email"     => ["required", "email",/* "unique:users",*/Rule::unique('users', 'email')->ignore($request->id)],
                "phone"     => ["required", "numeric",/* "unique:users"*/Rule::unique('users', 'phone')->ignore($request->id)]
            ]
           );
            
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            try{

                DB::beginTransaction();
                $data = $this->getModel();
                $data->created_by = $request->user()->id ?? null;

                $data->state_id = $request->state_id;
                $data->country_id = $request->country_id;
                $data->blood_group_id = $request->blood_group_id;
                $data->source = $request->source;
                $data->first_name = $request->first_name;                  
                $data->last_name = $request->last_name;
                
                //$file_path = $this->uploadFile($request, 'file', $this->patient_uploads,720);
                
                if($request->hasFile('image')){
                    $data->image_url = $this->uploadFile($request, 'image', $this->patient_uploads, null,null,$data->image_url);
                }
                
                //$data->image =  $request-> image;
                //$data->image_url =  $request-> image_url;
                // $data->patient_picture_name = $imageName;            
                // $data->patient_picture_location = $imageUrl;            
                $data->email = $request->email;
                $data->phone = $request->phone;
                $data->alternet_phone = $request->alternet_phone;
                $data->password = !empty($request->password) ? bcrypt($request->password) : $data->password;
                $data->address = $request->address;
                $data->area = $request->area;
                $data->city = $request->city;
                $data->bsn_number = $request->bsn_number;
                $data->dob_number = $request->dob_number;
                $data->insurance_number = $request->insurance_number;
                $data->emergency_contact = $request->emergency_contact;
                $data->age = $request->age;
                $data->gender = $request->gender;
                $data->marital_status = $request->marital_status;
                $data->medical_history = $request->medical_history;
                //$data->date_of_birth = Carbon::now();
                $data->date_of_birth = $request->date_of_birth;
                $data->occupation = $request->occupation;
                $data->remarks = $request->remarks ?? '';
                //$data->password = bcrypt($request->email);
                if($request->hasFile('picture')){
                    $data->image_url = $this->uploadFile($request, 'picture', $this->patient_uploads, null,null,$data->image_url);
                }
                $data->patientstatus=$request->patientstatus;
                $data->save();
                $this->saveFileInfo($request, $data);
                
                DB::commit();
                try{
                    event(new AccountRegistration($data, "patient"));
                }catch(Exception $e){
                    
                }
            }
            catch(Exception $e){
                return $this->apiOutput($this->getError( $e), 500);
                DB::rollBack();
            }
            $this->apiSuccess("Patient Info Added Successfully");
            $this->data = (new UserResource($data));
            return $this->apiOutput(); 
                   
            }
            catch(Exception $e){
            
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
   


    /**
     * Save File Info
     */
    public function saveFileInfo($request, $patient){

        $file_path = $this->uploadFile($request, 'file', $this->patient_uploads,720);
      
          
        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){

                $data = new PatientUpload();
                $data->created_by   = $request->user()->id;
                $data->patient_id   = $patient->id;
                $data->file_name    = $request->file_name ?? "Paitent Upload";
                $data->file_url     = $path;
                $data->file_type    = $request->file_type;
                $data->status       = $request->status;
                $data->remarks      = $request->remarks ?? '';
                $data->save();

            }
      
    }


   
   public function updateFileInfo($request, $id){
        $upload_files = $this->uploadFile($request, 'file', $this->patient_uploads);
        if( is_array($upload_files) ){
            foreach($upload_files as $file){
                $upload = new PatientUpload();
                $upload->patient_id = $id;
                $upload->file_name    = $request->file_name ?? "Patient Upload Updated";
                $upload->file_url     = $file;
                $upload->save();    
            }
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
            $paitent = User::find($request->id);
            if( empty($paitent) ){
                return $this->apiOutput("Patient Data Not Found", 400);
            }
            $this->data = (new UserResource($paitent));
            $this->apiSuccess("Patient Showed Successfully");
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
        // $temp=User::find($id);
        // return $temp;

        // $temp=User::all();
        // return $temp;
        //return $id;
        try{
            $validator = Validator::make($request->all(), [
                "id"            => ["required", "exists:users,id"],
                //'first_name'    => 'required',
                //'last_name'     => 'required',
                "email"         => ["required", "email",/* "unique:users",*/Rule::unique('users', 'email')->ignore($request->id)],
                "phone"         => ["required", "numeric",/* "unique:users"*/Rule::unique('users', 'phone')->ignore($request->id)]
            ]);
                
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
    
            DB::beginTransaction();
            $data = $this->getModel()->find($request->id);
            $data->updated_by = $request->user()->id ?? null;
            

            $data->state_id = $request->state_id;
            $data->country_id = $request->country_id;
            $data->blood_group_id = $request->blood_group_id;
            $data->source = $request->source;
            $data->first_name = $request->first_name;                  
            $data->last_name = $request->last_name;
            // $data->patient_picture_name = $imageName;            
            // $data->patient_picture_location = $imageUrl;            
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->alternet_phone = $request->alternet_phone;
            // $data->password = !empty($request->password) ? bcrypt($request->password) : $data->password;
            $data->address = $request->address;
            $data->area = $request->area;
            $data->city = $request->city;
            $data->bsn_number = $request->bsn_number;
            $data->dob_number = $request->dob_number;
            $data->insurance_number = $request->insurance_number;
            $data->emergency_contact = $request->emergency_contact;
            $data->age = $request->age;
            $data->gender = $request->gender;
            $data->marital_status = $request->marital_status;
            $data->medical_history = $request->medical_history;
            //$data->date_of_birth = Carbon::now();
            $data->date_of_birth = $request->date_of_birth;
            $data->occupation = $request->occupation;
            $data->remarks = $request->remarks ?? '';
            $data->password = bcrypt($request->password);
            if($request->hasFile('picture')){
                $data->image_url = $this->uploadFile($request, 'picture', $this->patient_uploads, null,null,$data->image_url);
            }
            $data->patientstatus=$request->patientstatus;
            //$this->updateFileInfo($request, $data);

            $data->save();
            $this->updateFileInfo($request, $data->id);

            DB::commit();
            //try{
                // event(new Registered($data));
           // }catch(Exception $e){
                //
            //}

            $this->apiSuccess("Patient Info Updated Successfully");
            $this->data = (new UserResource($data));
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
    public function destroy($id)
    {
        try{
            $data = $this->getModel()->find($id);
            if( empty($data) ){
                return $this->apiOutput("Data Not Found", 400);
            }
            PatientUpload::where('patient_id',$data->id)->delete();
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("Patient Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    public function deleteFilePatient(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:patient_uploads,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $patientupload=PatientUpload::where('id',$request->id);
            $patientupload->delete();
            $this->apiSuccess("Patient File Deleted successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    public function addFilePatient(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "patient_id"            => ["required","exists:users,id"],

            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $this->saveAddFileInfo($request);
            $this->apiSuccess("Patient Info Added Successfully");
            return $this->apiOutput();
           
           
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Save File Info
     */
    public function saveAddFileInfo($request){

        $file_path = $this->uploadFile($request, 'file', $this->patient_uploads,720);

        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){

                $data = new PatientUpload();
                $data->created_by   = $request->user()->id;
                $data->patient_id   = $request->patient_id;
                $data->file_name    = $request->file_name ?? "Paitent Upload";
                $data->file_url     = $path;
                $data->file_type    = $request->file_type ;
                $data->status       = $request->status;
                $data->remarks      = $request->remarks ?? '';
                $data->save();            

            }
      
    }

    public function updatePatientFileInfo(Request $request){
        try{
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:patient_uploads,id"],

            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $data = PatientUpload::find($request->id);

            if($request->hasFile('picture')){
                $data->file_url = $this->uploadFile($request, 'picture', $this->patient_uploads, null,null,$data->file_url);
            }

            $data->save();
            
            $this->apiSuccess("Patient File Updated Successfully");
            
            return $this->apiOutput();
           
           
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


}
