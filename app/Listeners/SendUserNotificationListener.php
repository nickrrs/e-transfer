<?php

namespace App\Listeners;

use App\Events\SendUserNotification;
use App\Exceptions\UnableServiceExcpetion;
use App\Mail\SendUserNotificationMail;
use App\Services\SendMail\SendMailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUserNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private SendMailService $sendMailService, private Log $log)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SendUserNotification $event): void
    {

        if (!$this->sendMailService->isNotificationServiceStable()) {
            // handle job for emails
            $this->log->critical("[The notification service is unable at the moment]");
        }

        Mail::to($event->payeeInfo->email)->send(new SendUserNotificationMail($event->transaction));
    }
}
