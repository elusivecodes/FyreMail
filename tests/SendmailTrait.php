<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Mail\Mail;
use Fyre\Mail\Mailer;
use Fyre\Mail\Handlers\SendmailMailer;

trait SendmailTrait
{

    public static function setUpBeforeClass(): void
    {
        Mailer::setAppCharset('utf-8');

        Mail::clear();

        Mail::setConfig('default', [
            'className' =>  SendmailMailer::class
        ]);
    }

}
