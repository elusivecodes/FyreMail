<?php
declare(strict_types=1);

namespace Tests\Email;

use
    Fyre\Mail\Email,
    Fyre\Mail\Exceptions\MailException;

trait FormatTest
{

    public function testDefaultFormat(): void
    {
        $this->assertEquals(
            Email::TEXT,
            $this->email->getFormat()
        );
    }

    public function testSetFormat(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setFormat(Email::TEXT)
        );

        $this->assertEquals(
            Email::TEXT,
            $this->email->getFormat()
        );
    }

    public function testSetFormatHtml(): void
    {
        $this->email->setFormat(Email::HTML);

        $this->assertEquals(
            Email::HTML,
            $this->email->getFormat()
        );
    }

    public function testSetFormatBoth(): void
    {
        $this->email->setFormat(Email::BOTH);

        $this->assertEquals(
            Email::BOTH,
            $this->email->getFormat()
        );
    }

    public function testSetFormatInvalid(): void
    {
        $this->expectException(MailException::class);

        $this->email->setFormat('invalid');
    }

}
