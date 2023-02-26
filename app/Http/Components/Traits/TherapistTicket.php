<?php

namespace App\Http\Components\Traits;

use App\Models\TicketAssignTherapist;

trait TherapistTicket{
    /**
     * Assign Therapist Into Ticket
     */
    protected function AssignTherapistIntoTicket($ticket_id, $therapist = null){    
        TicketAssignTherapist::where("ticket_id", $ticket_id)->delete();
        if( !is_array($therapist) && !empty($therapist) ){
            $therapist = (array) $therapist;
        }
        
        if( is_array($therapist) ){
            foreach($therapist as $therapist_id){
                $ticket_assign_therapist = new TicketAssignTherapist();
                $ticket_assign_therapist->ticket_id     = $ticket_id;
                $ticket_assign_therapist->therapist_id  = $therapist_id;
                $ticket_assign_therapist->save();
            }
        }
    }
}