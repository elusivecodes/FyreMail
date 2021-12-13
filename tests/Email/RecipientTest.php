<?php
declare(strict_types=1);

namespace Tests\Email;

trait RecipientTest
{

    public function testRecipients(): void
    {
        $this->email->setBcc('test1@test.com');
        $this->email->setCc('test2@test.com');
        $this->email->setTo('test3@test.com');

        $this->assertEquals(
            [
                'test1@test.com' => 'test1@test.com',
                'test2@test.com' => 'test2@test.com',
                'test3@test.com' => 'test3@test.com'
            ],
            $this->email->getRecipients()
        );
    }

}
