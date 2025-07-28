<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Container\Container;
use Fyre\Mail\Exceptions\MailException;
use Fyre\Mail\Handlers\SmtpMailer;
use Fyre\Mail\Mailer;
use Fyre\Mail\MailManager;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;

use function class_uses;

final class MailerTest extends TestCase
{
    protected MailManager $mailer;

    public function testFailedConnection(): void
    {
        $this->expectException(MailException::class);

        $this->mailer->use('invalid');
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

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(Mailer::class)
        );
    }

    protected function setUp(): void
    {
        $this->mailer = Container::getInstance()
            ->use(MailManager::class);
    }
}
