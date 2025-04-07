<?php

namespace Fyre\Mail\Handlers;

use Fyre\Mail\Email;
use Fyre\Mail\Mailer;

/**
 * DebugMailer
 */
class DebugMailer extends Mailer
{
    protected array $sentEmails = [];

    /**
     * Clear the sent emails.
     */
    public function clear(): void
    {
        $this->sentEmails = [];
    }

    /**
     * Get the sent emails.
     *
     * @return array The sent emails.
     */
    public function getSentEmails(): array
    {
        return $this->sentEmails;
    }

    /**
     * Send an email.
     *
     * @param Email $email The email to send.
     */
    public function send(Email $email): void
    {
        static::checkEmail($email);

        $this->sentEmails[] = [
            'headers' => $email->getFullHeaders(),
            'body' => $email->getFullBodyString(),
        ];
    }
}
