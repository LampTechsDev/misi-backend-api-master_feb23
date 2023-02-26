<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    public function createdBy(){
        return $this->belongsTo(Admin::class, "created_by")->withTrashed();
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class, "updated_by")->withTrashed();
    }
    public function patient(){
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function assignTherapist(){
        return $this->hasMany(TicketAssignTherapist::class, 'ticket_id');
    }
    public function ticketDepartment(){
        return $this->belongsTo(TicketDepartment::class, 'ticket_department_id'); 
    }
    public function replies(){
        return $this->hasMany(TicketReply::class, "ticket_id");
    }
    public function fileInfo(){
        return $this->hasMany(TicketUpload::class, 'ticket_id');
    }
    

   
}
