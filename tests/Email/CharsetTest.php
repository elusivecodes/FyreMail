<?php
declare(strict_types=1);

namespace Tests\Email;

trait CharsetTest
{

    public function testDefaultCharset(): void
    {
        $this->assertEquals(
            'utf-8',
            $this->email->getCharset()
        );
    }

    public function testSetCharset(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setCharset('iso-8859-1')
        );

        $this->assertEquals(
            'iso-8859-1',
            $this->email->getCharset()
        );
    }

}
