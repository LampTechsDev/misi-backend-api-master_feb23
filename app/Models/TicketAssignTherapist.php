<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAssignTherapist extends Model
{
    use HasFactory;

    public function therapist(){
        return $this->belongsTo(Therapist::class, 'therapist_id');
    }
    public function ticket(){
        return $this->belongsTo(Ticket::class, "ticket_id");
    }
}
