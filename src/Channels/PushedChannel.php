<?php

namespace Baronet\Notify\Channels;

use Baronet\Notify\Services\Pushed;
use Illuminate\Notifications\Notification;

class PushedChannel
{
    protected $client;

    public function __construct(Pushed $client)
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
        if (! $to = $notifiable->routeNotificationFor('mobile', $notification)) {
            return;
        }

        $data = $notification->getData();

        $this->client->sendMessage($to, $data['message'], $data['url'] ?? '');
    }
}
