<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Container\Container;
use Fyre\Mail\Email;
use Fyre\Mail\Handlers\SmtpMailer;
use Fyre\Mail\Mailer;
use Fyre\Mail\MailManager;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function getenv;

final class SMTPTest extends TestCase
{
    protected static Mailer $mailer;

    public function testMailSend(): void
    {
        $this->expectNotToPerformAssertions();

        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = self::$mailer->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->setBodyText('This is a test')
            ->send();
    }

    public function testMailSendAttachment(): void
    {
        $this->expectNotToPerformAssertions();

        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = self::$mailer->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'file' => 'test.jpg',
                ],
            ])
            ->setFormat(Email::HTML)
            ->send();
    }

    public function testMailSendAttachmentContent(): void
    {
        $this->expectNotToPerformAssertions();

        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = self::$mailer->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'content' => file_get_contents('test.jpg'),
                ],
            ])
            ->setFormat(Email::HTML)
            ->send();
    }

    public function testMailSendAttachmentInline(): void
    {
        $this->expectNotToPerformAssertions();

        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = self::$mailer->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'file' => 'test.jpg',
                    'contentId' => '1234',
                ],
            ])
            ->setFormat(Email::HTML)
            ->setBodyHtml('<img src="cid:1234">')
            ->send();
    }

    public function testMailSendHtml(): void
    {
        $this->expectNotToPerformAssertions();

        $mailTo = getenv('MAIL_TO');
        $mailFrom = getenv('MAIL_FROM');

        if (!$mailTo || !$mailFrom) {
            return;
        }

        $email = self::$mailer->email()
            ->setTo($mailTo)
            ->setFrom($mailFrom)
            ->setSubject('Test')
            ->setFormat(Email::HTML)
            ->setBodyHtml('<b>This is a test</b>')
            ->send();
    }

    public static function setUpBeforeClass(): void
    {
        self::$mailer = Container::getInstance()
            ->use(MailManager::class)
            ->build([
                'className' => SmtpMailer::class,
                'host' => getenv('SMTP_HOST'),
                'port' => getenv('SMTP_PORT'),
                'username' => getenv('SMTP_USERNAME'),
                'password' => getenv('SMTP_PASSWORD'),
                'auth' => getenv('SMTP_AUTH'),
                'tls' => getenv('SMTP_TLS'),
                'keepAlive' => true,
            ]);
    }
}
