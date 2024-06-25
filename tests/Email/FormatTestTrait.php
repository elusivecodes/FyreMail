<?php
declare(strict_types=1);

namespace Tests\Email;

use Fyre\Mail\Email;
use Fyre\Mail\Exceptions\MailException;

trait FormatTestTrait
{
    public function testDefaultFormat(): void
    {
        $this->assertSame(
            Email::TEXT,
            $this->email->getFormat()
        );
    }

    public function testSetFormat(): void
    {
        $this->assertSame(
            $this->email,
            $this->email->setFormat(Email::TEXT)
        );

        $this->assertSame(
            Email::TEXT,
            $this->email->getFormat()
        );
    }

    public function testSetFormatBoth(): void
    {
        $this->email->setFormat(Email::BOTH);

        $this->assertSame(
            Email::BOTH,
            $this->email->getFormat()
        );
    }

    public function testSetFormatHtml(): void
    {
        $this->email->setFormat(Email::HTML);

        $this->assertSame(
            Email::HTML,
            $this->email->getFormat()
        );
    }

    public function testSetFormatInvalid(): void
    {
        $this->expectException(MailException::class);

        $this->email->setFormat('invalid');
    }
}
