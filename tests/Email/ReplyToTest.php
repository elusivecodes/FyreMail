<?php
declare(strict_types=1);

namespace Tests\Email;

trait ReplyToTest
{

    public function testAddReplyTo(): void
    {
        $this->email->setReplyTo('test1@test.com');

        $this->assertEquals(
            $this->email,
            $this->email->addReplyTo('test2@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com',
                'test2@test.com' => 'test2@test.com'
            ],
            $this->email->getReplyTo()
        );
    }

    public function testAddReplyToName(): void
    {
        $this->email->setReplyTo('test1@test.com');
        $this->email->addReplyTo('test2@test.com', 'Test 2');

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com',
                'test2@test.com' => 'Test 2'
            ],
            $this->email->getReplyTo()
        );
    }

    public function testAddReplyToInvalid(): void
    {
        $this->email->setReplyTo('test1@test.com');
        $this->email->addReplyTo('test2');

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getReplyTo()
        );
    }

    public function testSetReplyTo(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setReplyTo('test1@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getReplyTo()
        );
    }

    public function testSetReplyToArray(): void
    {
        $this->email->setReplyTo([
            'test1@test.com' => 'Test 1'
        ]);

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1'
            ],
            $this->email->getReplyTo()
        );
    }

    public function testSetReplyToMultiple(): void
    {
        $this->email->setReplyTo([
            'test1@test.com' => 'Test 1',
            'test2@test.com' => 'Test 2'
        ]);

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1',
                'test2@test.com' => 'Test 2'
            ],
            $this->email->getReplyTo()
        );
    }

    public function testSetReplyToInvalid(): void
    {
        $this->email->setReplyTo('test1');

        $this->assertEquals(
            [],
            $this->email->getReplyTo()
        );
    }

    public function testHeaderReplyTo(): void
    {
        $this->email->setReplyTo('test1@test.com');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com',
            $headers['Reply-To']
        );
    }

    public function testHeaderReplyToName(): void
    {
        $this->email->setReplyTo([
            'test1@test.com' => 'Test'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test>',
            $headers['Reply-To']
        );
    }

    public function testHeaderReplyToMultiple(): void
    {
        $this->email->setReplyTo([
            'test1@test.com' => 'Test 1',
            'test2@test.com' => 'Test 2'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test 1>, test2@test.com <Test 2>',
            $headers['Reply-To']
        );
    }

    public function testHeaderReplyToEncoding(): void
    {
        $this->email->setReplyTo([
            'test1@test.com' => 'Тестовое задание'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?UTF-8?B?0KLQtdGB0YLQvtCy0L7QtSDQt9Cw0LTQsNC90LjQtQ==?=>',
            $headers['Reply-To']
        );
    }

    public function testHeaderReplyToCharset(): void
    {
        $this->email->setCharset('iso-8859-1');
        $this->email->setReplyTo([
            'test1@test.com' => 'Тестовое задание'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?ISO-8859-1?B?Pz8/Pz8/Pz8gPz8/Pz8/Pw==?=>',
            $headers['Reply-To']
        );
    }

}
