<?php

namespace Baronet\Notify\Notifications;

use Illuminate\Support\Arr;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification as IlluminateNotification;

class Notification extends IlluminateNotification
{
    protected $data;

    public function via($notifiable): array
    {
        $availableChannels = config('notify.channels');

        if (empty($this->getData()['channels']) && is_array($availableChannels)) {
            return array_values($availableChannels);
        }

        return Arr::only($availableChannels, $this->getData()['channels']);
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getData()['subject'])
            ->line($this->getData()['message']);
    }

    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->from(config('env.APP_NAME'))
            ->to($this->getData()['to'] ?? config('notify.slack.channel'))
            ->content($this->getData()['message']);
    }

    public function toBrowser($notifiable)
    {
    }

    public function toMobile($notifiable)
    {
    }

    public function toSms($notifiable)
    {
    }

    public function toWhatsApp($notifiable)
    {
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
