<?php

namespace App\Listeners;

use App\Events\SendUserNotification;
use App\Mail\SendUserNotificationMail;
use App\Services\SendMail\SendMailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUserNotificationListener
{
    public function __construct(private SendMailService $sendMailService)
    {
    }

    public function handle(SendUserNotification $event): void
    {
        if (! $this->sendMailService->isNotificationServiceStable()) {
            Log::critical("[The notification service is unable at the moment]");
        }

        Mail::to($event->payeeInfo->email)->send(new SendUserNotificationMail($event->transaction));
    }
}
