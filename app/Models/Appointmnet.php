<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointmnet extends Model
{
    use HasFactory;
    public function createdBy(){
        return $this->belongsTo(Admin::class, "created_by")->withTrashed();
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class, "updated_by")->withTrashed();
    }
    public function patient(){       
        return $this->belongsTo(User::class, 'patient_id');        
    }
    public function therapist(){       
        return $this->belongsTo(Therapist::class, 'therapist_id');        
    }
    public function ticket(){       
        return $this->belongsTo(Ticket::class, 'ticket_id');        
    }
    public function schedule(){       
        return $this->belongsTo(TherapistSchedule::class, 'therapist_schedule_id');        
    }

    public function fileInfo(){
        return $this->hasMany(AppointmentUpload::class, 'appointment_id');
    }

    public function intake(){
        return $this->hasMany(AppointmentIntake::class, 'appointment_id');
    }
    
}
