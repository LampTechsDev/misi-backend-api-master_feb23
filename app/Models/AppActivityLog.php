<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppActivityLog extends Model
{
    use HasFactory;
    public function advisor(){
        return $this->belongsTo(User::class, "advisor_id")->withTrashed();
    }
    public function admin(){
        return $this->belongsTo(Admin::class, "admin_id")->withTrashed();
    }
    
}
