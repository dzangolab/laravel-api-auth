<?php

namespace Dzangolab\Auth\Listeners;

use Dzangolab\Auth\Events\UserWasCreated;
use Dzangolab\Auth\Mail\UserConfirmationMessage;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserWasCreatedListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(UserWasCreated $event)
    {
        $featureUserConfirmation = (bool) config('dzangolabAuth.user_confirmation');

        if (!$featureUserConfirmation) {
            return;
        }

        $user = $event->getUser();

        $message = new UserConfirmationMessage($user, $this->getLocale());

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

    protected function send(UserConfirmationMessage $message)
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
