<?php
declare(strict_types=1);

namespace Tests\Email;

use Fyre\Mail\Email;
use Fyre\Mail\Handlers\SendmailMailer;
use Fyre\Mail\MailManager;
use PHPUnit\Framework\TestCase;

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

    protected function setUp(): void
    {
        $this->email = (new MailManager([], 'utf-8'))->build([
            'className' => SendmailMailer::class,
        ])->email();
    }
}
