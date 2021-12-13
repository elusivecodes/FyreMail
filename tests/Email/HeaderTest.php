<?php
declare(strict_types=1);

namespace Tests\Email;

use
    DateTime,
    Fyre\Mail\Email;

use const
    DATE_RFC2822;

trait HeaderTest
{

    public function testSetHeaders(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setHeaders([
                'Test1' => 'A'
            ])
        );

        $headers = $this->email->getHeaders();

        $this->assertEquals(
            'A',
            $headers['Test1']
        );
    }

    public function testSetHeadersMultiple(): void
    {
        $this->email->setHeaders([
            'Test1' => 'A',
            'Test2' => 'B'
        ]);

        $headers = $this->email->getHeaders();

        $this->assertEquals(
            'A',
            $headers['Test1']
        );

        $headers = $this->email->getHeaders();

        $this->assertEquals(
            'B',
            $headers['Test2']
        );
    }

    public function testHeaderHeaders(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setHeaders([
                'Test1' => 'A'
            ])
        );

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'A',
            $headers['Test1']
        );
    }

    public function testHeaderDate(): void
    {
        $headers = $this->email->getFullHeaders();

        $this->assertInstanceOf(
            DateTime::class,
            DateTime::createFromFormat(DATE_RFC2822, $headers['Date'])
        );
    }

    public function testHeaderMessageId(): void
    {
        $headers = $this->email->getFullHeaders();

        $this->assertMatchesRegularExpression(
            '/<[a-z0-9]{42}@.*>/',
            $headers['Message-ID']
        );
    }

    public function testHeaderMIMEVersion(): void
    {
        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            '1.0',
            $headers['MIME-Version']
        );
    }

    public function testHeaderContentType(): void
    {
        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'text/plain; charset=utf-8',
            $headers['Content-Type']
        );
    }

    public function testHeaderContentTypeHtml(): void
    {
        $this->email->setFormat(Email::HTML);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'text/html; charset=utf-8',
            $headers['Content-Type']
        );
    }

    public function testHeaderContentTypeBoth(): void
    {
        $this->email->setFormat(Email::BOTH);

        $headers = $this->email->getFullHeaders();
        $boundary = $this->email->getBoundary();

        $this->assertEquals(
            'multipart/alternative; boundary="'.$boundary.'"',
            $headers['Content-Type']
        );
    }

    public function testHeaderContentTypeAttachments(): void
    {
        $this->email->addAttachments([
            'test.jpg' => [
                'file' => 'test.jpg'
            ]
        ]);

        $headers = $this->email->getFullHeaders();
        $boundary = $this->email->getBoundary();

        $this->assertEquals(
            'multipart/mixed; boundary="'.$boundary.'"',
            $headers['Content-Type']
        );
    }

    public function testHeaderContentTypeCharset(): void
    {
        $this->email->setCharset('iso-8859-1');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'text/plain; charset=iso-8859-1',
            $headers['Content-Type']
        );
    }

    public function testHeaderContentTypeCharsetHtml(): void
    {
        $this->email->setCharset('iso-8859-1');
        $this->email->setFormat(Email::HTML);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'text/html; charset=iso-8859-1',
            $headers['Content-Type']
        );
    }

    public function testHeaderEncoding(): void
    {
        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'base64',
            $headers['Content-Transfer-Encoding']
        );
    }

}
