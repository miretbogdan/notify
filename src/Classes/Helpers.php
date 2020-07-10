<?php

namespace Baronet\Notify\Classes;

class Helpers
{
    public static function sendExceptionNotification($message, $exception)
    {
        $notifiable = app(config('notify.notifiable'));

        $notification = app(config('notify.exceptions.notification'))->setEvent([
            'message' => $message,
            'exception' => $exception,
        ]);
    
        $notifiable->notify($notification);
    }
}
