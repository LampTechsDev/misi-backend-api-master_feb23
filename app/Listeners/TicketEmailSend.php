<?php

namespace App\Listeners;

use App\Events\TicketRaise;
use App\Http\Components\Classes\Facade\TemplateMessage;
use App\Jobs\SendMail;
use App\Models\EmailTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TicketEmailSend
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TicketRaise  $event
     * @return void
     */
    public function handle(TicketRaise $event)
    {
        $ticket         = $event->ticket;
        $ticket_type    = $event->ticket_type;
        $email_template = null;

        /**
         * Load Appointmrnt Email Template Based On appointment_type
         */
        switch ($ticket_type) {
            case 'ticket_update':
                $email_template = EmailTemplate::where("email_type", "ticket_update")->orderBy("id", "DESC")->first();
                break;
            default:
            $email_template = EmailTemplate::where("email_type", "ticket_create")->orderBy("id", "DESC")->first();
                break;
        }

        if( isset($email_template->template) && $email_template->mail_send){
            $message = TemplateMessage::model($ticket)->parse($email_template->template);
            
            $patient = $ticket->patient;
            $therapist = $ticket->therapist;

            // Send Mail to Paitent
            if( !empty($patient) ){
                SendMail::dispatch($patient, $email_template->subject, $message, $email_template->cc)->delay(1);
            }

            // Send Mail to Therapist
            if( !empty($therapist) ){
                SendMail::dispatch($therapist, $email_template->subject, $message, $email_template->cc)->delay(1);
            }
        }
    }
}
