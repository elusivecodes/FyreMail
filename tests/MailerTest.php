<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Mail\Mail,
    Fyre\Mail\Mailer,
    Fyre\Mail\Exceptions\MailException,
    Fyre\Mail\Handlers\SmtpMailer,
    PHPUnit\Framework\TestCase;

final class MailerTest extends TestCase
{

    use
        SMTPTrait;

    public function testFailedConnection(): void
    {
        $this->expectException(MailException::class);

        Mail::use('invalid');
    }

    public function testGetCharset(): void
    {
        $this->assertEquals(
            'iso-8559-1',
            Mail::load([
                'charset' => 'iso-8559-1',
                'className' => SmtpMailer::class
            ])->getCharset()
        );
    }

    public function testGetClient(): void
    {
        $this->assertEquals(
            'test',
            Mail::load([
                'client' => 'test',
                'className' => SmtpMailer::class
            ])->getClient()
        );
    }

    public function testGetAppCharset(): void
    {
        $this->assertEquals(
            'utf-8',
            Mailer::getAppCharset()
        );
    }

}
