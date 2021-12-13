<?php
declare(strict_types=1);

namespace Tests\Email;

trait PriorityTest
{

    public function testSetPriority(): void
    {
        $this->assertEquals(
            $this->email,
            $this->email->setPriority(1)
        );

        $this->assertEquals(
            1,
            $this->email->getPriority()
        );
    }

    public function testSetPriorityNull(): void
    {
        $this->email->setPriority(null);

        $this->assertEquals(
            null,
            $this->email->getPriority()
        );
    }

    public function testHeaderPriority(): void
    {
        $this->email->setPriority(1);

        $headers = $this->email->getFullHeaders();

        $this->assertEquals(
            '1',
            $headers['X-Priority']
        );
    }

}
