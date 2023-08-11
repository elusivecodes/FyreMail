<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Mail\Mail;
use Fyre\Mail\Exceptions\MailException;
use Fyre\Mail\Handlers\SendmailMailer;
use PHPUnit\Framework\TestCase;

final class MailTest extends TestCase
{

    public function testGetConfig(): void
    {
        $this->assertSame(
            [
                'default' => [
                    'className' =>  SendmailMailer::class
                ],
                'other' => [
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

    public function testIsLoaded(): void
    {
        Mail::use();
        
        $this->assertTrue(
            Mail::isLoaded()
        );
    }

    public function testIsLoadedKey(): void
    {
        Mail::use('other');
        
        $this->assertTrue(
            Mail::isLoaded('other')
        );
    }

    public function testIsLoadedInvalid(): void
    {
        $this->assertFalse(
            Mail::isLoaded('test')
        );
    }

    public function testLoad(): void
    {
        $this->assertInstanceOf(
            SendmailMailer::class,
            Mail::load([
                'className' => SendmailMailer::class
            ])
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

    public function testUnload(): void
    {
        Mail::use();

        $this->assertTrue(
            Mail::unload()
        );

        $this->assertFalse(
            Mail::isLoaded()
        );
        $this->assertFalse(
            Mail::hasConfig()
        );
    }

    public function testUnloadKey(): void
    {
        Mail::use('other');

        $this->assertTrue(
            Mail::unload('other')
        );

        $this->assertFalse(
            Mail::isLoaded('other')
        );
        $this->assertFalse(
            Mail::hasConfig('other')
        );
    }

    public function testUnloadInvalid(): void
    {
        $this->assertFalse(
            Mail::unload('test')
        );
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

    protected function setUp(): void
    {
        Mail::clear();

        Mail::setConfig([
            'default' => [
                'className' =>  SendmailMailer::class
            ],
            'other' => [
                'className' =>  SendmailMailer::class
            ]
        ]);
    }

}
