<?php
declare(strict_types=1);

namespace Tests\Email;

use Fyre\Container\Container;
use Fyre\Mail\Email;
use Fyre\Mail\Handlers\SendmailMailer;
use Fyre\Mail\MailManager;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;

use function class_uses;

final class EmailTest extends TestCase
{
    use AttachmentTestTrait;
    use BccTestTrait;
    use BodyTestTrait;
    use BoundaryTestTrait;
    use CcTestTrait;
    use CharsetTestTrait;
    use FormatTestTrait;
    use FromTestTrait;
    use HeaderTestTrait;
    use PriorityTestTrait;
    use ReadReceiptTestTrait;
    use RecipientTestTrait;
    use ReplyToTestTrait;
    use ReturnPathTestTrait;
    use SenderTestTrait;
    use SubjectTestTrait;
    use ToTestTrait;

    protected Email $email;

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(Email::class)
        );
    }

    protected function setUp(): void
    {
        $this->email = Container::getInstance()
            ->use(MailManager::class)
            ->build([
                'className' => SendmailMailer::class,
            ])
            ->email();
    }
}
