<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Therapist extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public function createdBy(){
        return $this->belongsTo(Admin::class, "created_by")->withTrashed();
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class, "updated_by")->withTrashed();
    }
    
    public function fileInfo(){
        return $this->hasMany(TherapistUpload::class, 'therapist_id');
    }

    public function blood(){
        return $this->belongsTo(BloodGroup::class, 'blood_group_id');
    }
    public function country(){
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function state(){
        return $this->belongsTo(State::class, 'state_id');
    }
    public function therapistType(){
        return $this->belongsTo(TherapistType::class, 'therapist_type_id'); 
    }
}
