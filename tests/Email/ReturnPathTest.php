<?php
declare(strict_types=1);

namespace Tests\Email;

trait ReturnPathTest
{

    public function testSetReturnPath(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setReturnPath('test1@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getReturnPath()
        );
    }

    public function testSetReturnPathName(): void
    {
        $this->email->setReturnPath('test1@test.com', 'Test 1');

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1'
            ],
            $this->email->getReturnPath()
        );
    }

    public function testSetReturnPathInvalid(): void
    {
        $this->email->setReturnPath('test1');

        $this->assertEquals(
            [],
            $this->email->getReturnPath()
        );
    }

    public function testHeaderReturnPath(): void
    {
        $this->email->setReturnPath('test1@test.com');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com',
            $headers['Return-Path']
        );
    }

    public function testHeaderReturnPathName(): void
    {
        $this->email->setReturnPath('test1@test.com', 'Test');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test>',
            $headers['Return-Path']
        );
    }

    public function testHeaderReturnPathEncoding(): void
    {
        $this->email->setReturnPath('test1@test.com', 'Тестовое задание');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?UTF-8?B?0KLQtdGB0YLQvtCy0L7QtSDQt9Cw0LTQsNC90LjQtQ==?=>',
            $headers['Return-Path']
        );
    }

    public function testHeaderReturnPathCharset(): void
    {
        $this->email->setCharset('iso-8859-1');
        $this->email->setReturnPath('test1@test.com', 'Тестовое задание');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?ISO-8859-1?B?Pz8/Pz8/Pz8gPz8/Pz8/Pw==?=>',
            $headers['Return-Path']
        );
    }

}
