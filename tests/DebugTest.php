<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Container\Container;
use Fyre\Mail\Email;
use Fyre\Mail\Handlers\DebugMailer;
use Fyre\Mail\Mailer;
use Fyre\Mail\MailManager;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

final class DebugTest extends TestCase
{
    protected Mailer $mailer;

    public function testMailSend(): void
    {
        $this->mailer->email()
            ->setTo('test1@test.com')
            ->setFrom('test2@test.com')
            ->setSubject('Test')
            ->setBodyText('This is a test')
            ->send();

        $sentEmail = $this->mailer->getSentEmails()[0] ?? [];

        $this->assertSame(
            'test2@test.com',
            $sentEmail['headers']['From'] ?? ''
        );

        $this->assertSame(
            'test1@test.com',
            $sentEmail['headers']['To'] ?? ''
        );

        $this->assertSame(
            'Test',
            $sentEmail['headers']['Subject'] ?? ''
        );

        $this->assertSame(
            '1.0',
            $sentEmail['headers']['MIME-Version'] ?? ''
        );

        $this->assertSame(
            'text/plain; charset=utf-8',
            $sentEmail['headers']['Content-Type'] ?? ''
        );

        $this->assertSame(
            'base64',
            $sentEmail['headers']['Content-Transfer-Encoding'] ?? ''
        );

        $this->assertSame(
            'This is a test'."\r\n\r\n",
            $sentEmail['body'] ?? ''
        );
    }

    public function testMailSendAttachment(): void
    {
        $email = $this->mailer->email()
            ->setTo('test1@test.com')
            ->setFrom('test2@test.com')
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'file' => 'test.jpg',
                ],
            ])
            ->setFormat(Email::HTML);
        $email->send();

        $boundary = $email->getBoundary();
        $sentEmail = $this->mailer->getSentEmails()[0] ?? [];

        $this->assertSame(
            'test2@test.com',
            $sentEmail['headers']['From'] ?? ''
        );

        $this->assertSame(
            'test1@test.com',
            $sentEmail['headers']['To'] ?? ''
        );

        $this->assertSame(
            'Test',
            $sentEmail['headers']['Subject'] ?? ''
        );

        $this->assertSame(
            '1.0',
            $sentEmail['headers']['MIME-Version'] ?? ''
        );

        $this->assertSame(
            'multipart/mixed; boundary="'.$boundary.'"',
            $sentEmail['headers']['Content-Type'] ?? ''
        );

        $this->assertSame(
            'base64',
            $sentEmail['headers']['Content-Transfer-Encoding'] ?? ''
        );

        $this->assertStringStartsWith(
            '--'.$boundary."\r\n",
            $sentEmail['body'] ?? ''
        );
    }

    public function testMailSendAttachmentContent(): void
    {
        $email = $this->mailer->email()
            ->setTo('test1@test.com')
            ->setFrom('test2@test.com')
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'content' => file_get_contents('test.jpg'),
                ],
            ])
            ->setFormat(Email::HTML);
        $email->send();

        $boundary = $email->getBoundary();
        $sentEmail = $this->mailer->getSentEmails()[0] ?? [];

        $this->assertSame(
            'test2@test.com',
            $sentEmail['headers']['From'] ?? ''
        );

        $this->assertSame(
            'test1@test.com',
            $sentEmail['headers']['To'] ?? ''
        );

        $this->assertSame(
            'Test',
            $sentEmail['headers']['Subject'] ?? ''
        );

        $this->assertSame(
            '1.0',
            $sentEmail['headers']['MIME-Version'] ?? ''
        );

        $this->assertSame(
            'multipart/mixed; boundary="'.$boundary.'"',
            $sentEmail['headers']['Content-Type'] ?? ''
        );

        $this->assertSame(
            'base64',
            $sentEmail['headers']['Content-Transfer-Encoding'] ?? ''
        );

        $this->assertStringStartsWith(
            '--'.$boundary."\r\n",
            $sentEmail['body'] ?? ''
        );
    }

    public function testMailSendAttachmentInline(): void
    {
        $email = $this->mailer->email()
            ->setTo('test1@test.com')
            ->setFrom('test2@test.com')
            ->setSubject('Test')
            ->addAttachments([
                'test.jpg' => [
                    'file' => 'test.jpg',
                    'contentId' => '1234',
                ],
            ])
            ->setFormat(Email::HTML)
            ->setBodyHtml('<img src="cid:1234">');
        $email->send();

        $boundary = $email->getBoundary();
        $sentEmail = $this->mailer->getSentEmails()[0] ?? [];

        $this->assertSame(
            'test2@test.com',
            $sentEmail['headers']['From'] ?? ''
        );

        $this->assertSame(
            'test1@test.com',
            $sentEmail['headers']['To'] ?? ''
        );

        $this->assertSame(
            'Test',
            $sentEmail['headers']['Subject'] ?? ''
        );

        $this->assertSame(
            '1.0',
            $sentEmail['headers']['MIME-Version'] ?? ''
        );

        $this->assertSame(
            'multipart/mixed; boundary="'.$boundary.'"',
            $sentEmail['headers']['Content-Type'] ?? ''
        );

        $this->assertSame(
            'base64',
            $sentEmail['headers']['Content-Transfer-Encoding'] ?? ''
        );

        $this->assertStringStartsWith(
            '--'.$boundary."\r\n",
            $sentEmail['body'] ?? ''
        );

        $this->assertStringContainsString(
            '<img src="cid:1234">',
            $sentEmail['body'] ?? ''
        );
    }

    public function testMailSendHtml(): void
    {
        $this->mailer->email()
            ->setTo('test1@test.com')
            ->setFrom('test2@test.com')
            ->setSubject('Test')
            ->setFormat(Email::HTML)
            ->setBodyHtml('<b>This is a test</b>')
            ->send();

        $sentEmail = $this->mailer->getSentEmails()[0] ?? [];

        $this->assertSame(
            'test2@test.com',
            $sentEmail['headers']['From'] ?? ''
        );

        $this->assertSame(
            'test1@test.com',
            $sentEmail['headers']['To'] ?? ''
        );

        $this->assertSame(
            'Test',
            $sentEmail['headers']['Subject'] ?? ''
        );

        $this->assertSame(
            '1.0',
            $sentEmail['headers']['MIME-Version'] ?? ''
        );

        $this->assertSame(
            'text/html; charset=utf-8',
            $sentEmail['headers']['Content-Type'] ?? ''
        );

        $this->assertSame(
            'base64',
            $sentEmail['headers']['Content-Transfer-Encoding'] ?? ''
        );

        $this->assertSame(
            '<b>This is a test</b>'."\r\n\r\n",
            $sentEmail['body'] ?? ''
        );
    }

    protected function setUp(): void
    {
        $this->mailer = Container::getInstance()
            ->use(MailManager::class)
            ->build([
                'className' => DebugMailer::class,
            ]);
    }
}
