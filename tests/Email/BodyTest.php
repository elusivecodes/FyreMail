<?php
declare(strict_types=1);

namespace Tests\Email;

use
    Fyre\Mail\Email;

trait BodyTest
{

    public function testSetBody(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setBody([
                Email::TEXT => 'Test',
                Email::HTML => '<b>Test</b>'
            ])
        );

        $this->assertEquals(
            'Test',
            $this->email->getBodyText()
        );

        $this->assertEquals(
            '<b>Test</b>',
            $this->email->getBodyHtml()
        );
    }

    public function testSetBodyText(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setBodyText('Test')
        );

        $this->assertEquals(
            'Test',
            $this->email->getBodyText()
        );
    }

    public function testSetBodyHtml(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setBodyHtml('<b>Test</b>')
        );

        $this->assertEquals(
            '<b>Test</b>',
            $this->email->getBodyHtml()
        );
    }

}
