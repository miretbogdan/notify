<?php

namespace Baronet\Notify\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification as IlluminateNotification;
use Illuminate\Support\Str;
use Baronet\Notify\Classes\ThrottledNotification;

class ExceptionNotification extends IlluminateNotification implements ThrottledNotification
{
    protected $event;

    public function via($notifiable): array
    {
        return config('notify.exceptions.channels');
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent($event): self
    {
        $this->event = $event;

        return $this;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject($this->getEvent()['message'])
            ->line("Exception message: {$this->getEvent()['exception']->getMessage()}")
            ->line("Exception: {$this->getEvent()['exception']->getTraceAsString()}");
    }

    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->error()
            ->from(config('env.APP_NAME'))
            ->to(config('notify.exceptions.slack.channel'))
            ->content($this->getEvent()['message'])
            ->attachment(function (SlackAttachment $attachment) {
                $attachment->fields([
                    'Exception message' => $this->getEvent()['exception']->getMessage(),
                ]);
            });
    }

    public function throttleDecayMinutes(): int
    {
        return config('notify.throttle_decay');
    }

    public function throttleKeyId()
    {
        if ($this->getEvent()['exception'] instanceof \Exception) {
            return Str::kebab($this->getEvent()['exception']->getMessage());
        }

        // fall back throttle key, use the notification name...
        return static::class;
    }
}
