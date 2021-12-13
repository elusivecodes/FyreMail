<?php
declare(strict_types=1);

namespace Tests\Email;

trait AttachmentTest
{

    public function testAddAttachments(): void
    {
        $this->email->setAttachments([
            'test1.jpg' => [
                'file' => 'test1.jpg'
            ]
        ]);

        $this->email->addAttachments([
            'test2.jpg' => [
                'file' => 'test2.jpg'
            ]
        ]);

        $this->assertEquals(
            [
                'test1.jpg' => [
                    'file' => 'test1.jpg'
                ],
                'test2.jpg' => [
                    'file' => 'test2.jpg'
                ]
            ],
            $this->email->getAttachments()
        );
    }

    public function testSetAttachments(): void
    {
        $this->email->setAttachments([
            'test.jpg' => [
                'file' => 'test.jpg'
            ]
        ]);

        $this->assertEquals(
            [
                'test.jpg' => [
                    'file' => 'test.jpg'
                ]
            ],
            $this->email->getAttachments()
        );
    }

}
