<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Mail\Mail,
    Fyre\Mail\Mailer,
    Fyre\Mail\Handlers\SmtpMailer;

use function
    getenv;

trait SMTPTrait
{

    public static function setUpBeforeClass(): void
    {
        Mailer::setAppCharset('utf-8');

        Mail::clear();

        Mail::setConfig('default', [
            'className' =>  SmtpMailer::class,
            'host' => getenv('SMTP_HOST'),
            'port' => getenv('SMTP_PORT'),
            'username' => getenv('SMTP_USERNAME'),
            'password' => getenv('SMTP_PASSWORD'),
            'auth' => getenv('SMTP_AUTH'),
            'tls' => getenv('SMTP_TLS'),
            'keepAlive' => true
        ]);
    }

}
