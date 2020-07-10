<?php

namespace Baronet\Notify\Channels;

use Baronet\Notify\Services\Twilio;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    protected $client;

    public function __construct(Twilio $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('sms', $notification)) {
            return;
        }

        $data = $notification->getData();

        $this->client->sendMessage($to, $data['message']);
    }
}
