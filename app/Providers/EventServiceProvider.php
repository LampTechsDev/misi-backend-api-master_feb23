<?php

namespace App\Providers;

use App\Events\AccountRegistration;
use App\Events\Appointment;
use App\Events\PasswordReset;
use App\Events\TicketRaise;
use App\Listeners\AccountSignupEmail;
use App\Listeners\AppointmentEmailSend;
use App\Listeners\PasswordResetEmailSend;
use App\Listeners\TicketEmailSend;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Account Signup Email
        AccountRegistration::class => [
            AccountSignupEmail::class
        ],

        // Appoinement Email Send
        Appointment::class => [
            AppointmentEmailSend::class,
        ],

        // Ticket Email
        TicketRaise::class => [
            TicketEmailSend::class,
        ],

        // Password Reset
        PasswordReset::class => [
            PasswordResetEmailSend::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
