<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Mail\Exceptions\MailException;
use Fyre\Mail\Handlers\SmtpMailer;
use Fyre\Mail\MailManager;
use PHPUnit\Framework\TestCase;

final class MailerTest extends TestCase
{
    protected MailManager $mailer;

    public function testFailedConnection(): void
    {
        $this->expectException(MailException::class);

        $this->mailer->use('invalid');
    }

    public function testGetAppCharset(): void
    {
        $this->assertSame(
            'utf-8',
            $this->mailer->getAppCharset()
        );
    }

    public function testGetClient(): void
    {
        $this->assertSame(
            'test',
            $this->mailer->build([
                'client' => 'test',
                'className' => SmtpMailer::class,
            ])->getClient()
        );
    }

    protected function setUp(): void
    {
        $this->mailer = new MailManager([], 'utf-8');
    }
}
