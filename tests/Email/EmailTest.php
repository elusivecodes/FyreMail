<?php
declare(strict_types=1);

namespace Tests\Email;

use
    Fyre\Mail\Email,
    Fyre\Mail\Mail,
    PHPUnit\Framework\TestCase,
    Tests\SendmailTrait;

final class EmailTest extends TestCase
{

    protected Email $email;

    use
        AttachmentTest,
        BccTest,
        BodyTest,
        BoundaryTest,
        CcTest,
        CharsetTest,
        FormatTest,
        FromTest,
        HeaderTest,
        PriorityTest,
        ReadReceiptTest,
        RecipientTest,
        ReplyToTest,
        ReturnPathTest,
        SenderTest,
        SendmailTrait,
        SubjectTest,
        ToTest;

    protected function setUp(): void
    {
        $this->email = Mail::use()->email();
    }

}
