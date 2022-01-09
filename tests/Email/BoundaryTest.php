<?php
declare(strict_types=1);

namespace Tests\Email;

use
    Fyre\Mail\Email;

trait BoundaryTest
{

    public function testGetBoundary(): void
    {
        $this->assertMatchesRegularExpression(
            '/[a-z0-9]{32}/',
            $this->email->getBoundary()
        );
    }

    public function testGetBoundaryPersists(): void
    {
        $this->assertSame(
            $this->email->getBoundary(),
            $this->email->getBoundary()
        );
    }

}
