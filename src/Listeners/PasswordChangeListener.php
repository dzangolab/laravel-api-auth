<?php

namespace Dzangolab\Auth\Listeners;

use Dzangolab\Auth\Events\PasswordChangedEvent;
use Dzangolab\Auth\Mail\PasswordWasChangedMessage;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordChangeListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(PasswordChangedEvent $event)
    {
        $user = $event->getUser();

        $message = new PasswordWasChangedMessage($user, $this->getLocale());

        $this->send($message);
    }

    protected function failed($exception)
    {
        Log::error($exception);
    }

    protected function getLocale()
    {
        return App::getLocale();
    }

    protected function send(PasswordWasChangedMessage $message)
    {
        try {
            $message->onQueue('emails');

            Mail::to($message->getRecipient())
                ->queue($message);
        } catch (Exception $exception) {
            $this->failed($exception);
        }
    }
}
