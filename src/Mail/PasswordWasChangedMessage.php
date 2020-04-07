<?php

namespace Dzangolab\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordWasChangedMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    const SUBJECT = 'emails.user.subject.password_changed';

    const TEMPLATE = 'password_changed';

    protected $id;

    protected $template_locale;

    protected $user;

    public function __construct($user, $locale = 'en')
    {
        $this->user = $user;

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

    protected function getUser()
    {
        return $this->user;
    }
}
