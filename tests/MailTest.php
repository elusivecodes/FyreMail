<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Mail\Mail,
    Fyre\Mail\Exceptions\MailException,
    Fyre\Mail\Handlers\SendmailMailer,
    PHPUnit\Framework\TestCase;

final class MailTest extends TestCase
{

    use
        SendmailTrait;

    public function testLoadInvalidHandler(): void
    {
        $this->expectException(MailException::class);

        Mail::load([
            'className' => 'Invalid'
        ]);
    }

    public function testUse(): void
    {
        $handler1 = Mail::use();
        $handler2 = Mail::use();

        $this->assertSame($handler1, $handler2);

        $this->assertInstanceOf(
            SendmailMailer::class,
            $handler1
        );
    }

}
