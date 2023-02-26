<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PibFormula extends Model
{
    use HasFactory, SoftDeletes;

    public function createdBy(){
        return $this->belongsTo(Admin::class, "created_by")->withTrashed();
    }
    public function updatedBy(){
        return $this->belongsTo(Admin::class, "updated_by")->withTrashed();
    }
    public function patient(){
        return $this->belongsTo(User::class, 'patient_id')->withTrashed();
    }
    public function Ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id')->withTrashed();
    }

}
