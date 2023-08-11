<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Mail\Email;
use Fyre\Mail\Mail;
use Fyre\Mail\Mailer;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function getenv;

final class SMTPTest extends TestCase
{

    protected Mailer $mail;

    use SMTPTrait;

    /**
     * @doesNotPerformAssertions
     */
    public function testMailSend(): void
    {
        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = $this->mail->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->setBodyText('This is a test')
            ->send();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testMailSendHtml(): void
    {
        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = $this->mail->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->setFormat(Email::HTML)
            ->setBodyHtml('<b>This is a test</b>')
            ->send();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testMailSendAttachment(): void
    {
        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = $this->mail->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'file' => 'test.jpg'
                ]
            ])
            ->setFormat(Email::HTML)
            ->send();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testMailSendAttachmentInline(): void
    {
        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = $this->mail->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'file' => 'test.jpg',
                    'contentId' => '1234'
                ]
            ])
            ->setFormat(Email::HTML)
            ->setBodyHtml('<img src="cid:1234">')
            ->send();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testMailSendAttachmentContent(): void
    {
        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = $this->mail->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'content' => file_get_contents('test.jpg')
                ]
            ])
            ->setFormat(Email::HTML)
            ->send();
    }

    protected function setUp(): void
    {
        $this->mail = Mail::use();
    }

}
