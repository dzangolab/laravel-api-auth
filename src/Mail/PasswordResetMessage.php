<?php

namespace Dzangolab\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    const SUBJECT = 'emails.user.subject.password_reset';

    const TEMPLATE = 'password_reset';

    protected $app_url;

    protected $id;

    protected $template_locale;

    protected $token;

    protected $user;

    public function __construct($user, $app_url, $token, $locale = 'en')
    {
        $this->user = $user;

        $this->app_url = $app_url;

        $this->token = $token;

        $this->template_locale = $locale;

        $this->subject(null);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->getTemplate())
            ->with(
                [
                    'user' => $this->getUser(),
                    'url' => $this->getUrl(),
                    'sent_email_id' => $this->getId(),
                ]
            );
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRecipient()
    {
        return $this->getUser()->email;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function subject($subject)
    {
        $this->subject = $this->getSubject();

        return $this;
    }

    protected function getAppUrl()
    {
        return $this->app_url;
    }

    protected function getLocale()
    {
        return $this->template_locale;
    }

    protected function getSubject()
    {
        return trans(static::SUBJECT);
    }

    protected function getTemplate()
    {
        return sprintf(
            'emails.user.%s.%s',
            $this->getLocale(),
            static::TEMPLATE
        );
    }

    protected function getToken()
    {
        return $this->token;
    }

    protected function getUrl()
    {
        return sprintf(
            '%s/reset-password/%s',
            $this->getAppUrl(),
            $this->getToken()
        );
    }

    protected function getUser()
    {
        return $this->user;
    }
}
