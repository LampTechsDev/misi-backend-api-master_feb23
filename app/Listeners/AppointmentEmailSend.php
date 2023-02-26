<?php

namespace App\Listeners;

use App\Events\Appointment;
use App\Http\Components\Classes\Facade\TemplateMessage;
use App\Jobs\SendMail;
use App\Models\EmailTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

class AppointmentEmailSend
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
     * @param  \App\Events\Appointment  $event
     * @return void
     */
    public function handle(Appointment $event)
    {
        $appointment = $event->appointment;
        $appointment_type = $event->appointment_type;
        $email_template = null;

        /**
         * Load Appointmrnt Email Template Based On appointment_type
         */
        switch ($appointment_type) {
            case 'reschedule':
                $email_template = EmailTemplate::where("email_type", "appointment_reschedule")->orderBy("id", "DESC")->first();
                break;
            case 'cancel':
                $email_template = EmailTemplate::where("email_type", "appointment_cancellation")->orderBy("id", "DESC")->first();
                    break;
            default:
            $email_template = EmailTemplate::where("email_type", "appointment_confirmation")->orderBy("id", "DESC")->first();
                break;
        }

        if( isset($email_template->template) && $email_template->mail_send){
            $message = TemplateMessage::model($appointment)->parse($email_template->template);
            
            $patient = $appointment->patient;
            $therapist = $appointment->therapist;
            $invoice_pdf = null;
            if( !empty($appointment->invoice_url) && Storage::disk("public")->exists($appointment->invoice_url) ){
                $invoice_pdf = public_path('storage/'.$appointment->invoice_url);
            }
           

            // Send Mail to Paitent
            if( !empty($patient) ){
                SendMail::dispatch($patient, $email_template->subject, $message, $email_template->cc, $invoice_pdf)->delay(1);
            }

            // Send Mail to Therapist
            if( !empty($therapist) ){
                SendMail::dispatch($therapist, $email_template->subject, $message, $email_template->cc, $invoice_pdf)->delay(1);
            }
        }


    }
}
