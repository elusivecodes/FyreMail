<?php
declare(strict_types=1);

namespace Tests\Email;

trait SubjectTest
{

    public function testSetSubject(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setSubject('Test')
        );

        $this->assertEquals(
            'Test',
            $this->email->getSubject()
        );
    }

    public function testHeaderSubject(): void
    {
        $this->email->setSubject('Test');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            'Test',
            $headers['Subject']
        );
    }

    public function testHeaderSubjectEncoding(): void
    {
        $this->email->setSubject('Тестовое задание');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            '=?UTF-8?B?0KLQtdGB0YLQvtCy0L7QtSDQt9Cw0LTQsNC90LjQtQ==?=',
            $headers['Subject']
        );
    }

    public function testHeaderSubjectCharset(): void
    {
        $this->email->setCharset('iso-8859-1');
        $this->email->setSubject('Тестовое задание');

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            '=?ISO-8859-1?B?Pz8/Pz8/Pz8gPz8/Pz8/Pw==?=',
            $headers['Subject']
        );
    }

}
