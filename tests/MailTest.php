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

    public function testGetConfig(): void
    {
        $this->assertSame(
            [
                'default' => [
                    'className' =>  SendmailMailer::class
                ]
            ],
            Mail::getConfig()
        );
    }

    public function testGetConfigKey(): void
    {
        $this->assertSame(
            [
                'className' =>  SendmailMailer::class
            ],
            Mail::getConfig('default')
        );
    }
    
    public function testGetKey(): void
    {
        $handler = Mail::use();

        $this->assertSame(
            'default',
            Mail::getKey($handler)
        );
    }

    public function testGetKeyInvalid(): void
    {
        $handler = Mail::load([
            'className' => SendmailMailer::class
        ]);

        $this->assertSame(
            null,
            Mail::getKey($handler)
        );
    }
    
    public function testLoadInvalidHandler(): void
    {
        $this->expectException(MailException::class);

        Mail::load([
            'className' => 'Invalid'
        ]);
    }

    public function testSetConfig(): void
    {
        Mail::setConfig([
            'test' => [
                'className' => SendmailMailer::class
            ]
        ]);

        $this->assertSame(
            [
                'className' => SendmailMailer::class
            ],
            Mail::getConfig('test')
        );
    }

    public function testSetConfigExists(): void
    {
        $this->expectException(MailException::class);

        Mail::setConfig('default', [
            'className' => SendmailMailer::class
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
