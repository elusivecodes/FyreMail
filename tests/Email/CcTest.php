<?php
declare(strict_types=1);

namespace Tests\Email;

trait CcTest
{

    public function testAddCc(): void
    {
        $this->email->setCc('test1@test.com');

        $this->assertEquals(
            $this->email,
            $this->email->addCc('test2@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com',
                'test2@test.com' => 'test2@test.com'
            ],
            $this->email->getCc()
        );
    }

    public function testAddCcName(): void
    {
        $this->email->setCc('test1@test.com');
        $this->email->addCc('test2@test.com', 'Test 2');

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com',
                'test2@test.com' => 'Test 2'
            ],
            $this->email->getCc()
        );
    }

    public function testAddCcInvalid(): void
    {
        $this->email->setCc('test1@test.com');
        $this->email->addCc('test2');

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getCc()
        );
    }

    public function testSetCc(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setCc('test1@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getCc()
        );
    }

    public function testSetCcArray(): void
    {
        $this->email->setCc([
            'test1@test.com' => 'Test 1'
        ]);

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1'
            ],
            $this->email->getCc()
        );
    }

    public function testSetCcMultiple(): void
    {
        $this->email->setCc([
            'test1@test.com' => 'Test 1',
            'test2@test.com' => 'Test 2'
        ]);

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1',
                'test2@test.com' => 'Test 2'
            ],
            $this->email->getCc()
        );
    }

    public function testSetCcInvalid(): void
    {
        $this->email->setCc('test1');

        $this->assertEquals(
            [],
            $this->email->getCc()
        );
    }

    public function testHeaderCc(): void
    {
        $this->email->setCc('test1@test.com');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com',
            $headers['Cc']
        );
    }

    public function testHeaderCcName(): void
    {
        $this->email->setCc([
            'test1@test.com' => 'Test'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test>',
            $headers['Cc']
        );
    }

    public function testHeaderCcMultiple(): void
    {
        $this->email->setCc([
            'test1@test.com' => 'Test 1',
            'test2@test.com' => 'Test 2'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test 1>, test2@test.com <Test 2>',
            $headers['Cc']
        );
    }

    public function testHeaderCcEncoding(): void
    {
        $this->email->setCc([
            'test1@test.com' => 'Тестовое задание'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?UTF-8?B?0KLQtdGB0YLQvtCy0L7QtSDQt9Cw0LTQsNC90LjQtQ==?=>',
            $headers['Cc']
        );
    }

    public function testHeaderCcCharset(): void
    {
        $this->email->setCharset('iso-8859-1');
        $this->email->setCc([
            'test1@test.com' => 'Тестовое задание'
        ]);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?ISO-8859-1?B?Pz8/Pz8/Pz8gPz8/Pz8/Pw==?=>',
            $headers['Cc']
        );
    }

}
