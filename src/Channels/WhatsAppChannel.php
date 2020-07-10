<?php

namespace Baronet\Notify\Channels;

use Baronet\Notify\Services\WaboxApp;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    protected $client;

    const DEFAULT_TYPE = 'text';

    public function __construct(WaboxApp $client)
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
        if (! $to = $notifiable->routeNotificationFor('whatsApp', $notification)) {
            return;
        }

        $data = $notification->getData();

        if (empty($data['type'])) {
            $type = self::DEFAULT_TYPE;
        }

        $method = 'send' . ucfirst($type);

        if (!method_exists($this->client, $method)) {
            return;
        }

        $this->client->{$method}($to, $data['from'] ?? config('notify.whatsApp.from'), $data['message'], $data['args'] ?? []);
    }
}
