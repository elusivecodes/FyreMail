<?php
declare(strict_types=1);

namespace Tests\Email;

trait FromTest
{

    public function testSetFrom(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setFrom('test1@test.com')
        );

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com'
            ],
            $this->email->getFrom()
        );
    }

    public function testSetFromName(): void
    {
        $this->email->setFrom('test1@test.com', 'Test 1');

        $this->assertEquals(
            [
                'test1@test.com' => 'Test 1'
            ],
            $this->email->getFrom()
        );
    }

    public function testSetFromInvalid(): void
    {
        $this->email->setFrom('test1');

        $this->assertEquals(
            [],
            $this->email->getFrom()
        );
    }

    public function testHeaderFrom(): void
    {
        $this->email->setFrom('test1@test.com');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com',
            $headers['From']
        );
    }

    public function testHeaderFromName(): void
    {
        $this->email->setFrom('test1@test.com', 'Test');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <Test>',
            $headers['From']
        );
    }

    public function testHeaderFromEncoding(): void
    {
        $this->email->setFrom('test1@test.com', 'Тестовое задание');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?UTF-8?B?0KLQtdGB0YLQvtCy0L7QtSDQt9Cw0LTQsNC90LjQtQ==?=>',
            $headers['From']
        );
    }

    public function testHeaderFromCharset(): void
    {
        $this->email->setCharset('iso-8859-1');
        $this->email->setFrom('test1@test.com', 'Тестовое задание');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'test1@test.com <=?ISO-8859-1?B?Pz8/Pz8/Pz8gPz8/Pz8/Pw==?=>',
            $headers['From']
        );
    }

}
