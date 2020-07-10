<?php

return [

    /*
     * The notification that will be sent.
     */
    'notification' => \Baronet\Notify\Notifications\Notification::class,

    /*
     * The notifiable to which the notification will be sent. The default
     * notifiable will use the configurations specified
     * in this config file.
     */
    'notifiable' => \Baronet\Notify\Notifiables\Notifiable::class,

    /*
     * By default notifications are sent on every call. You can pass a callable to filter
     * out certain notifications. The given callable will receive the notification. If the callable
     * return false, the notification will not be sent.
     */
    'notificationFilter' => null,

    /*
     * The length of the throttle window in minutes. Eg: 10 would mean
     * only one notification of certain type would be actually sent
     * within a 10 minute window...
    */
    'throttle_decay' => 10,

    /*
     * The channels to which the notification will be sent.
     */
    'channels' => [
        'mail',
        'slack',
        'browser' => \Baronet\Notify\Channels\BrowserChannel::class,
        'mobile' => \Baronet\Notify\Channels\PushedChannel::class,
        'sms' => \Baronet\Notify\Channels\SmsChannel::class,
        'whatsApp' => \Baronet\Notify\Channels\WhatsAppChannel::class
    ],

    'mail' => [
        'to' => 'xxxxxxxxxxxx',
    ],
    'slack' => [
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
        'channel' => 'xxxxxxxxxxxx'
    ],
    'whatsApp' => [
        'from' => 'xxxxxxxxxxxx',
        'to' => 'xxxxxxxxxxxx',
    ],
    'sms' => [
        'to' => 'xxxxxxxxxxxx',
    ],
    'browser' => [
        'to' => 'onesignal_user_APP_ID',
    ],
    'pushed' => [
        'to' => 'pushed_user_APP_ID',
    ],

    'exceptions' => [
        'notification' => \Baronet\Notify\Notifications\ExceptionNotification::class,
        'channels' => [
            'slack'
        ],
        'slack' => [
            'channel' => 'xxxxxxxxxxxx'
        ]
    ]

];
