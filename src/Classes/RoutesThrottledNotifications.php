<?php

namespace Baronet\Notify\Classes;

use Illuminate\Cache\RateLimiter;
use Illuminate\Notifications\RoutesNotifications;
use Illuminate\Support\Str;

/**
 * Trait RoutesThrottledNotifications.
 */
trait RoutesThrottledNotifications
{
    use RoutesNotifications {
        RoutesNotifications::notify as parentNotify;
    }

    public function notify($instance): void
    {
        if (!$instance instanceof ThrottledNotification) {
            // Execute the original notify() method.
            $this->parentNotify($instance);

            return;
        }

        $key = $this->throttleKey($instance);

        if ($this->limiter()->tooManyAttempts($key, $this->maxAttempts())) {
            return;
        }

        $this->limiter()->hit($key, ($instance->throttleDecayMinutes() * 60));

        $this->parentNotify($instance);
    }

    /**
     * Build the notification throttle key from the Notification class name,
     * the Notification's throttle key id.
     * @param ThrottledNotification $instance
     * @return string
     */
    protected function throttleKey(ThrottledNotification $instance)
    {
        return Str::kebab(
            class_basename($instance) . '-' . $instance->throttleKeyId()
        );
    }

    /**
     * Get the rate limiter instance.
     */
    protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }

    /**
     * Set the max attempts to 1.
     */
    protected function maxAttempts()
    {
        return 1;
    }
}
