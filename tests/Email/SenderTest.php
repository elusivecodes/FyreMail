<?php
declare(strict_types=1);

namespace Tests\Email;

trait SenderTest
{

    public function testSetSender(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setSender('test1@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getSender()
        );
    }

    public function testSetSenderName(): void
    {
        $this->email->setSender('test1@test.com', 'Test 1');

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1'
            ],
            $this->email->getSender()
        );
    }

    public function testSetSenderInvalid(): void
    {
        $this->email->setSender('test1');

        $this->assertEquals(
            [],
            $this->email->getSender()
        );
    }

    public function testHeaderSender(): void
    {
        $this->email->setSender('test1@test.com');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com',
            $headers['Sender']
        );
    }

    public function testHeaderSenderName(): void
    {
        $this->email->setSender('test1@test.com', 'Test');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test>',
            $headers['Sender']
        );
    }

    public function testHeaderSenderEncoding(): void
    {
        $this->email->setSender('test1@test.com', 'Тестовое задание');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?UTF-8?B?0KLQtdGB0YLQvtCy0L7QtSDQt9Cw0LTQsNC90LjQtQ==?=>',
            $headers['Sender']
        );
    }

    public function testHeaderSenderCharset(): void
    {
        $this->email->setCharset('iso-8859-1');
        $this->email->setSender('test1@test.com', 'Тестовое задание');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?ISO-8859-1?B?Pz8/Pz8/Pz8gPz8/Pz8/Pw==?=>',
            $headers['Sender']
        );
    }

}
