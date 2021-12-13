<?php
declare(strict_types=1);

namespace Tests\Email;

trait BccTest
{

    public function testAddBcc(): void
    {
        $this->email->setBcc('test1@test.com');

        $this->assertEquals(
            $this->email,
            $this->email->addBcc('test2@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com',
                'test2@test.com' => 'test2@test.com'
            ],
            $this->email->getBcc()
        );
    }

    public function testAddBccName(): void
    {
        $this->email->setBcc('test1@test.com');
        $this->email->addBcc('test2@test.com', 'Test 2');

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com',
                'test2@test.com' => 'Test 2'
            ],
            $this->email->getBcc()
        );
    }

    public function testAddBccInvalid(): void
    {
        $this->email->setBcc('test1@test.com');
        $this->email->addBcc('test2');

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getBcc()
        );
    }

    public function testSetBcc(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setBcc('test1@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getBcc()
        );
    }

    public function testSetBccArray(): void
    {
        $this->email->setBcc([
            'test1@test.com' => 'Test 1'
        ]);

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1'
            ],
            $this->email->getBcc()
        );
    }

    public function testSetBccMultiple(): void
    {
        $this->email->setBcc([
            'test1@test.com' => 'Test 1',
            'test2@test.com' => 'Test 2'
        ]);

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1',
                'test2@test.com' => 'Test 2'
            ],
            $this->email->getBcc()
        );
    }

    public function testSetBccInvalid(): void
    {
        $this->email->setBcc('test1');

        $this->assertEquals(
            [],
            $this->email->getBcc()
        );
    }

    public function testHeaderBcc(): void
    {
        $this->email->setBcc('test1@test.com');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com',
            $headers['Bcc']
        );
    }

    public function testHeaderBccName(): void
    {
        $this->email->setBcc([
            'test1@test.com' => 'Test'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test>',
            $headers['Bcc']
        );
    }

    public function testHeaderBccMultiple(): void
    {
        $this->email->setBcc([
            'test1@test.com' => 'Test 1',
            'test2@test.com' => 'Test 2'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test 1>, test2@test.com <Test 2>',
            $headers['Bcc']
        );
    }

    public function testHeaderBccEncoding(): void
    {
        $this->email->setBcc([
            'test1@test.com' => 'Тестовое задание'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?UTF-8?B?0KLQtdGB0YLQvtCy0L7QtSDQt9Cw0LTQsNC90LjQtQ==?=>',
            $headers['Bcc']
        );
    }

    public function testHeaderBccCharset(): void
    {
        $this->email->setCharset('iso-8859-1');
        $this->email->setBcc([
            'test1@test.com' => 'Тестовое задание'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?ISO-8859-1?B?Pz8/Pz8/Pz8gPz8/Pz8/Pw==?=>',
            $headers['Bcc']
        );
    }

}
