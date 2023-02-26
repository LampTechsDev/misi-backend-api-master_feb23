<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentIntake extends Model
{
    use HasFactory;
    
    public function appointment(){       
        return $this->belongsTo(Appointmnet::class, 'appointment_id');        
    }
}
