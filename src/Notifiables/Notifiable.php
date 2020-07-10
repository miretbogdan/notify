<?php

namespace Baronet\Notify\Notifiables;

use Baronet\Notify\Classes\RoutesThrottledNotifications;

class Notifiable
{
    use RoutesThrottledNotifications;

    public function routeNotificationForMail(): string
    {
        return config('notify.mail.to');
    }

    public function routeNotificationForSlack(): string
    {
        return config('notify.slack.webhook_url');
    }

    public function routeNotificationForWhatsApp(): string
    {
        return config('notify.whatsApp.to');
    }

    public function routeNotificationForSms(): string
    {
        return config('notify.sms.to');
    }

    public function routeNotificationForBrowser(): string
    {
        return config('notify.browser.to');
    }

    public function routeNotificationForMobile(): string
    {
        return config('notify.pushed.to');
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return 1;
    }
}
