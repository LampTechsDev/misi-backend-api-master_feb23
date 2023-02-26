<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    public function userable(){
        return $this->morphTo(__FUNCTION__, "userable_type", "userable_id");
    }
    public function logable(){
        return $this->morphTo(__FUNCTION__, "tableable_type", "tableable_id");
    }
    // public function groupId(){
    //     return $this->belongsTo(Group::class, "group_id");
    // }

}
