<?php

namespace Dzangolab\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserConfirmationMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    const SUBJECT = 'dzangolab-auth::emails.user.subject.confirmation';

    const TEMPLATE = 'confirmation';

    protected $template_locale;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param string $locale
     */
    public function __construct($user, $locale = 'en')
    {
        $this->user = $user;

        $this->template_locale = $locale;

        $this->subject(null);
    }

    public function build()
    {
        return $this->view($this->getTemplate())
            ->with([
                'user' => $this->getUser(),
                'url' => $this->getUrl(),
            ]);
    }

    public function getRecipient()
    {
        return $this->getUser()->email;
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
            'dzangolab-auth::emails.user.%s.%s',
            $this->getLocale(),
            static::TEMPLATE
        );
    }

    protected function getUrl()
    {
        // FIXME [UKS 2020-03-09] Move this url format to somewhere before this method call
        return sprintf(
            '%s/enable?token=%s',
            config('app.url'),
            $this->getUser()->confirmation_token
        );
    }

    protected function getUser()
    {
        return $this->user;
    }
}
