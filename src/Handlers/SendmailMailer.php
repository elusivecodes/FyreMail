<?php

namespace Fyre\Mail\Handlers;

use 
    Fyre\Mail\Email,
    Fyre\Mail\Exceptions\MailException,
    Fyre\Mail\Mailer;

use function
    error_get_last,
    mail;

/**
 * SendmailMailer
 */
class SendmailMailer extends Mailer
{

    /**
     * Send an email.
     * @param Email $email The email to send.
     * @throws MailException if the email could not be sent.
     */
    public function send(Email $email): void
    {
        static::checkEmail($email);

        $headers = $email->getFullHeaders();
        $body = $email->getFullBodyString();

        $to = $headers['To'] ?? '';
        $subject = $headers['Subject'] ?? '';

        unset($headers['To']);
        unset($headers['Subject']);

        if (!mail($to, $subject, $body, $headers)) {
            $error = error_get_last();
            throw MailException::forDeliveryFailed($error['message'] ?? '');
        }
    }

}
