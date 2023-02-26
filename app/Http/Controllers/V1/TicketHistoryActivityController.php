<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

use App\Http\Resources\TicketHistoryActivityResource;
use App\Http\Resources\UserActivityResource;
use App\Models\TicketHistoryActivity;
use App\Models\UserActivity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class TicketHistoryActivityController extends Controller
{
    public function index()
    {
        //return 10;
        // $temp=BloodGroup::all();
        // return $temp;
        try{
            $this->data = UserActivityResource::collection(UserActivity::all());
            $this->apiSuccess("Blood Group Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    

}
