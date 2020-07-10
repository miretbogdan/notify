<?php

namespace Baronet\Notify;

use GuzzleHttp\Client;
use Baronet\Notify\Services\Pushed;
use Baronet\Notify\Services\Twilio;
use Baronet\Notify\Channels\SmsChannel;
use Baronet\Notify\Channels\PushedChannel;
use Baronet\Notify\Channels\BrowserChannel;
use Baronet\Notify\Channels\WhatsAppChannel;
use Baronet\Notify\Services\WaboxApp;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class NotifyServiceProvider extends IlluminateServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/notify.php' => config_path('notify.php'),
        ], 'notify-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'notify-migrations');

        // $this->publishes([
        //     __DIR__.'/path/to/translations' => resource_path('lang/vendor/notify'),
        // ], 'translations');

        // $this->publishes([
        //     __DIR__.'/path/to/views' => resource_path('views/vendor/notify'),
        // ]);

        // if ($this->app->runningInConsole()) {
        //     $this->commands([
        //         FooCommand::class,
        //     ]);
        // }

        // $this->publishes([
        //     __DIR__.'/path/to/assets' => public_path('vendor/notify'),
        // ], 'public');

        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // $this->loadFactoriesFrom(__DIR__.'/path/to/factories');


        $this->app->make(Notify::class)->register();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // $this->app->singleton(Notify::class);

        $this->registerServices();

        $this->registerChannels();
    }

    protected function registerServices()
    {
        $this->app->singleton(Pushed::class, function ($app) {
            return new Pushed(
                new Client,
                config('services.pushed.key'),
                config('services.pushed.secret'),
                config('services.pushed.alias')
            );
        });

        $this->app->singleton(Twilio::class, function ($app) {
            return new Twilio(
                config('services.twilio.acc_id'),
                config('services.twilio.token'),
                config('services.twilio.number'),
                config('services.twilio.sandbox_code'),
                config('services.twilio.sandbox_number')
            );
        });

        $this->app->singleton(WaboxApp::class, function ($app) {
            return new WaboxApp(
                new Client,
                config('services.waboxApp.token'),
                config('services.waboxApp.uid')
            );
        });
    }

    protected function registerChannels()
    {
        $this->app->singleton(BrowserChannel::class, function ($app) {
            return new BrowserChannel();
        });

        $this->app->singleton(PushedChannel::class, function ($app) {
            $pushed = $app->make(Pushed::class);

            return new PushedChannel(
                $pushed
            );
        });

        $this->app->singleton(SmsChannel::class, function ($app) {
            $twilio = $app->make(Twilio::class);

            return new SmsChannel(
                $twilio
            );
        });

        $this->app->singleton(WhatsAppChannel::class, function ($app) {
            $waboxApp = $app->make(WaboxApp::class);

            return new WhatsAppChannel(
                $waboxApp
            );
        });
    }
}
