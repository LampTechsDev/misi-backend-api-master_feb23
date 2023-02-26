<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scale extends Model
{
    use HasFactory;
    public function createdBy(){
        return $this->belongsTo(Admin::class, "created_by")->withTrashed();
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class, "updated_by")->withTrashed();
    }
    
    public function question(){
        return $this->belongsTo(Question::class, 'question_id');
    }
    public function patient(){
        return $this->belongsTo(User::class, 'patient_id');
    }
    // public function scale(){
    //     return $this->belongsTo(Question::class, 'question_id');
    // }
}
