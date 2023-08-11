<?php

namespace Fyre\Mail;

use finfo;
use Fyre\Mail\Exceptions\MailException;

use const DATE_RFC2822;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_EMAIL;
use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

use function addcslashes;
use function array_column;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function base64_encode;
use function chunk_split;
use function count;
use function date;
use function explode;
use function file_get_contents;
use function filter_var;
use function implode;
use function in_array;
use function is_numeric;
use function mb_convert_encoding;
use function mb_encode_mimeheader;
use function md5;
use function preg_match;
use function preg_split;
use function random_bytes;
use function rtrim;
use function str_replace;
use function strlen;
use function time;
use function wordwrap;

/**
 * Email
 */
class Email
{

    public const HTML = 'html';
    public const TEXT = 'text';
    public const BOTH = 'both';

    protected Mailer $mailer;

    protected array $to = [];
    protected array $from = [];
    protected array $sender = [];
    protected array $replyTo = [];
    protected array $readReceipt = [];
    protected array $returnPath = [];
    protected array $cc = [];
    protected array $bcc = [];
    protected string $subject = '';
    protected array $headers = [];
    protected array $body = [];
    protected array $attachments = [];

    protected int|null $priority = null;
    protected string $charset = 'utf-8';
    protected string $messageId;

    protected string $format = self::TEXT;
    protected string $boundary;

    /**
     * New Email constructor.
     * @param Mailer $mailer The mailer.
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;

        $this->charset = $this->mailer->getCharset();
    }

    /**
     * Add attachments.
     * @param array $attachments The attachments.
     * @return Email The email.
     */
    public function addAttachments(array $attachments): static
    {
        foreach ($attachments AS $filename => $attachment) {
            $this->attachments[$filename] = $attachment;
        }

        return $this;
    }

    /**
     * Add a BCC address.
     * @param string $email The email address.
     * @param string|null $name The name.
     * @return Email The Email.
     */
    public function addBcc(string $email, string|null $name = null): static
    {
        $email = static::validateEmail($email);

        if ($email) {
            $this->bcc[$email] = $name ?? $email;
        }

        return $this;
    }

    /**
     * Add a CC address.
     * @param string $email The email address.
     * @param string|null $name The name.
     * @return Email The Email.
     */
    public function addCc(string $email, string|null $name = null): static
    {
        $email = static::validateEmail($email);

        if ($email) {
            $this->cc[$email] = $name ?? $email;
        }

        return $this;
    }

    /**
     * Add a reply to address.
     * @param string $email The email address.
     * @param string|null $name The name.
     * @return Email The Email.
     */
    public function addReplyTo(string $email, string|null $name = null): static
    {
        $email = static::validateEmail($email);

        if ($email) {
            $this->replyTo[$email] = $name ?? $email;
        }

        return $this;
    }

    /**
     * Add a to address.
     * @param string $email The email address.
     * @param string|null $name The name.
     * @return Email The Email.
     */
    public function addTo(string $email, string|null $name = null): static
    {
        $email = static::validateEmail($email);

        if ($email) {
            $this->to[$email] = $name ?? $email;
        }

        return $this;
    }

    /**
     * Get the attachments.
     * @return array The attachments.
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * Get the BCC addresses.
     * @return array The BCC addresses.
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * Get the HTML body string.
     * @return string The HTML body string.
     */
    public function getBodyHtml(): string
    {
        return $this->body[static::HTML] ?? '';
    }

    /**
     * Get the text body string.
     * @return string The text body string.
     */
    public function getBodyText(): string
    {
        return $this->body[static::TEXT] ?? '';
    }

    /**
     * Get the boundary.
     * @param string The boundary.
     */
    public function getBoundary(): string
    {
        return $this->boundary ??= static::randomString();
    }

    /**
     * Get the CC addresses.
     * @return array The CC addresses.
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * Get the charset.
     * @return string The charset.
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * Get the email format.
     * @return string The email format.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get the from addresses.
     * @return array The from addresses.
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * Get the full email body lines.
     * @return array The body lines.
     */
    public function getFullBody(): array
    {
        $contentIds = array_filter(array_column($this->attachments, 'contentId'));

        $hasAttachments = $this->attachments !== [];
        $hasInlineAttachments = count($contentIds) > 0;
        $hasMultipleTypes = $this->format === static::BOTH;
        $multiPart = $hasAttachments || $hasMultipleTypes;

        $lines = [];

        $boundary = $textBoundary = $relatedBoundary = $this->getBoundary();

        if ($hasInlineAttachments) {
            $relatedBoundary = 'rel-'.$boundary;
            $textBoundary = $relatedBoundary;

            $lines[] = '--'.$this->getBoundary();
            $lines[] = 'Content-Type: multipart/related; boundary="'.$relatedBoundary.'"';
            $lines[] = '';
        }

        if ($this->format === static::BOTH && $hasAttachments) {
            $textBoundary = 'alt-boundary';

            $lines[] = '--'.$relatedBoundary;
            $lines[] = 'Content-Type: multipart/alternative; boundary="'.$textBoundary.'"';
            $lines[] = '';
        }

        if (in_array($this->format, [static::TEXT, static::BOTH])) {
            if ($multiPart) {
                $lines[] = '--'.$textBoundary;
                $lines[] = 'Content-Type: text/plain; charset='.$this->charset;
                $lines[] = 'Content-Transfer-Encoding: base64';
                $lines[] = '';
            }

            $content = static::prepareBody($this->body[static::TEXT] ?? '', $this->charset);

            $lines = array_merge($lines, $content);
            $lines[] = '';
            $lines[] = '';
        }

        if (in_array($this->format, [static::HTML, static::BOTH])) {
            if ($multiPart) {
                $lines[] = '--'.$textBoundary;
                $lines[] = 'Content-Type: text/html; charset='.$this->charset;
                $lines[] = 'Content-Transfer-Encoding: base64';
                $lines[] = '';
            }

            $content = static::prepareBody($this->body[static::HTML] ?? '', $this->charset);

            $lines = array_merge($lines, $content);
            $lines[] = '';
            $lines[] = '';
        }

        if ($textBoundary !== $relatedBoundary) {
            $lines[] = '--'.$textBoundary.'--';
            $lines[] = '';
        }

        if ($hasInlineAttachments) {
            $attachments = $this->attachFiles($relatedBoundary, true);

            $lines = array_merge($lines, $attachments);
            $lines[] = '';
            $lines[] = '--'.$relatedBoundary.'--';
            $lines[] = '';
        }

        if ($hasAttachments) {
            $attachments = $this->attachFiles($boundary);

            $lines = array_merge($lines, $attachments);
        }

        if ($multiPart) {
            $lines[] = '';
            $lines[] = '--'.$boundary.'--';
            $lines[] = '';
        }

        return $lines;
    }

    /**
     * Get the full email body string.
     * @return string The body string.
     */
    public function getFullBodyString(): string
    {
        $body = $this->getFullBody();

        return implode("\r\n", $body);
    }

    /**
     * Get the full email header lines.
     * @return array The email header lines.
     */
    public function getFullHeaders(): array
    {
        $headers = [];

        $addressHeaders = [
            'From' => 'from',
            'Reply-To' => 'replyTo',
            'Disposition-Notification-To' => 'readReceipt',
            'Return-Path' => 'returnPath',
            'To' => 'to',
            'Cc' => 'cc',
            'Bcc' => 'bcc'
        ];

        foreach ($addressHeaders AS $header => $property) {
            if ($this->$property === []) {
                continue;
            }

            $headers[$header] = $this->formatAddresses($this->$property);
        }

        if (array_key_first($this->sender) !== array_key_first($this->from)) {
            $headers['Sender'] = $this->formatAddresses($this->sender);
        }

        $headers['Date'] = date(DATE_RFC2822);
        $headers['Message-ID'] = $this->getMessageId();

        if ($this->priority) {
            $headers['X-Priority'] = $this->priority;
        }

        $headers['Subject'] = static::encodeForHeader($this->subject, $this->charset);
        $headers['MIME-Version'] = '1.0';

        if ($this->attachments !== []) {
            $headers['Content-Type'] = 'multipart/mixed; boundary="'.$this->getBoundary().'"';
        } else if ($this->format === static::BOTH) {
            $headers['Content-Type'] = 'multipart/alternative; boundary="'.$this->getBoundary().'"';
        } else if ($this->format === static::HTML) {
            $headers['Content-Type'] = 'text/html; charset='.$this->charset;
        } else if ($this->format === static::TEXT) {
            $headers['Content-Type'] = 'text/plain; charset='.$this->charset;
        }

        $headers['Content-Transfer-Encoding'] = 'base64';

        return array_merge($headers, $this->headers);
    }

    /**
     * Get the full email header string.
     * @return array The email header string.
     */
    public function getFullHeaderString(): string
    {
        $lines = $this->getFullHeaders();

        $headers = [];
        foreach ($lines AS $key => $value) {
            if ($value === [] || (!$value && $value !== '0')) {
                continue;
            }

            if (!is_array($value)) {
                $value = [$value];
            }

            foreach ($value AS $val) {
                $headers[] = $key.': '.$val;
            }
        }

        return implode("\r\n", $headers);
    }

    /**
     * Get the additional headers.
     * @return array The additional headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the message ID.
     * @return string The message ID.
     */
    public function getMessageId(): string
    {
        return $this->messageId ??= '<'.time().static::randomString().'@'.$this->mailer->getClient().'>';
    }

    /**
     * Get the priority.
     * @return int|null The priority.
     */
    public function getPriority(): int|null
    {
        return $this->priority;
    }

    /**
     * Get the read recipient addresses.
     * @return array The read recipient addresses.
     */
    public function getReadReceipt(): array
    {
        return $this->readReceipt;
    }

    /**
     * Get the recipient addresses.
     * @return array The recipient addresses.
     */
    public function getRecipients(): array
    {
        $to = $this->getTo();
        $cc = $this->getCc();
        $bcc = $this->getBcc();

        return array_merge($to, $cc, $bcc);
    }

    /**
     * Get the reply to addresses.
     * @return array The reply to addresses.
     */
    public function getReplyTo(): array
    {
        return $this->replyTo;
    }

    /**
     * Get the return path addresses.
     * @return array The return path addresses.
     */
    public function getReturnPath(): array
    {
        return $this->returnPath;
    }

    /**
     * Get the sender addresses.
     * @return array The sender addresses.
     */
    public function getSender(): array
    {
        return $this->sender;
    }

    /**
     * Get the subject.
     * @return string The subject.
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Get the to addresses.
     * @return array The to addresses.
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * Send the email.
     */
    public function send(): void
    {
        $this->mailer->send($this);
    }

    /**
     * Set the attachments.
     * @param array $attachments The attachments.
     * @return Email The email.
     */
    public function setAttachments(array $attachments): static
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Set the BCC address.
     * @param string|array $emails The email addresses.
     * @return Email The Email.
     */
    public function setBcc(string|array $emails): static
    {
        $this->bcc = static::parseEmails($emails);

        return $this;
    }

    /**
     * Set the body text and/or HTML.
     * @param array $body The body text and/or HTML.
     * @return Email The Email.
     */
    public function setBody(array $body): static
    {
        foreach ($body AS $type => $content) {
            $this->body[$type] = $content;
        }

        return $this;
    }

    /**
     * Set the body HTML.
     * @param string $content The content.
     * @return Email The Email.
     */
    public function setBodyHtml(string $content): static
    {
        return $this->setBody([static::HTML => $content]);
    }

    /**
     * Set the body text.
     * @param string $content The content.
     * @return Email The Email.
     */
    public function setBodyText(string $content): static
    {
        return $this->setBody([static::TEXT => $content]);
    }

    /**
     * Set the CC address.
     * @param string|array $emails The email addresses.
     * @return Email The Email.
     */
    public function setCc(string|array $emails): static
    {
        $this->cc = static::parseEmails($emails);

        return $this;
    }

    /**
     * Set the charset.
     * @param string $charset The charset.
     * @return Email The Email.
     */
    public function setCharset(string $charset): static
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * Set the email format.
     * @param string $format The email format.
     * @return Email The Email.
     * @throws MailException if the format is not valid.
     */
    public function setFormat(string $format): static
    {
        if (!in_array($format, [static::TEXT, static::HTML, static::BOTH])) {
            throw MailException::forInvalidFormat($format);
        }

        $this->format = $format;

        return $this;
    }

    /**
     * Set the from address.
     * @param string $emails The email address.
     * @param string|null $name The name.
     * @return Email The Email.
     */
    public function setFrom(string $email, string|null $name = null): static
    {
        $this->from = static::parseEmails([$email => $name]);

        return $this;
    }

    /**
     * Set additional headers.
     * @param array $headers The headers.
     * @return Email The Email.
     */
    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set the priority.
     * @param int|null $priority The priority.
     * @return Email The Email.
     */
    public function setPriority(int|null $priority = null): static
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Set the read receipt address.
     * @param string $emails The email address.
     * @param string|null $name The name.
     * @return Email The Email.
     */
    public function setReadReceipt(string $email, string|null $name = null): static
    {
        $this->readReceipt = static::parseEmails([$email => $name]);

        return $this;
    }

    /**
     * Set the reply to address.
     * @param string|array $emails The email addresses.
     * @return Email The Email.
     */
    public function setReplyTo(string|array $emails): static
    {
        $this->replyTo = static::parseEmails($emails);

        return $this;
    }

    /**
     * Set the return path address.
     * @param string $emails The email address.
     * @param string|null $name The name.
     * @return Email The Email.
     */
    public function setReturnPath(string $email, string|null $name = null): static
    {
        $this->returnPath = static::parseEmails([$email => $name]);

        return $this;
    }

    /**
     * Set the sender address.
     * @param string $emails The email address.
     * @param string|null $name The name.
     * @return Email The Email.
     */
    public function setSender(string $email, string|null $name = null): static
    {
        $this->sender = static::parseEmails([$email => $name]);

        return $this;
    }

    /**
     * Set the subject.
     * @param string $subject The subject.
     * @return Email The Email.
     */
    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the to addresses.
     * @param string|array $emails The email addresses.
     * @return Email The Email.
     */
    public function setTo(string|array $emails): static
    {
        $this->to = static::parseEmails($emails);

        return $this;
    }

    /**
     * Get attached files lines.
     * @param string $boundary The boundary.
     * @param bool $inline Whether to attach inline files.
     * @return array The attached file lines.
     * @throws MailException if an attachment is not valid.
     */
    protected function attachFiles(string $boundary, bool $inline = false): array
    {
        $lines = [];

        foreach ($this->attachments AS $filename => $attachment) {
            $attachment['contentId'] ??= null;

            if ($inline !== !!$attachment['contentId']) {
                continue;
            }

            if ($attachment['contentId']) {
                $attachment['disposition'] ??= 'inline';
            } else {
                $attachment['disposition'] ??= 'attachment';
            }
    
            if (array_key_exists('file', $attachment)) {
                $attachment['content'] ??= file_get_contents($attachment['file']);
            } else if (!array_key_exists('content', $attachment)) {
                throw MailException::forInvalidAttachment($filename);
            }

            $finfo = new finfo(FILEINFO_MIME);
            $mimeType = $finfo->buffer($attachment['content']);
            $attachment['mimeType'] ??= $mimeType;

            $attachment['content'] = chunk_split(base64_encode($attachment['content']));

            $lines[] = '--'.$boundary;
            $lines[] = 'Content-Type: '.$attachment['mimeType'].'; name="'.$filename.'"';
            $lines[] = 'Content-Disposition: '.$attachment['disposition'];
            $lines[] = 'Content-Transfer-Encoding: base64';

            if ($attachment['contentId']) {
                $lines[] = 'Content-ID: <'.$attachment['contentId'].'>';
            }

            $lines[] = '';
            $lines[] = $attachment['content'];
            $lines[] = '';
        }

        return $lines;
    }

    /**
     * Get formatted email addresses.
     * @param array $emails The email addresses.
     * @return string The formatted email addresses.
     */
    protected function formatAddresses(array $emails): string
    {
        $emails = array_map(
            function(string $email, string $alias): string {
                if ($email === $alias) {
                    return $email;
                }

                $encodedAlias = static::encodeForHeader($alias, $this->charset);

                if ($alias === $encodedAlias && preg_match('/[^a-z0-9 ]/i', $encodedAlias)) {
                    $encodedAlias = '"'.addcslashes($encodedAlias, '"').'"';
                }

                return $email.' <'.$encodedAlias.'>';
            },
            array_keys($emails),
            $emails
        );

        return implode(', ', $emails);
    }

    /**
     * Encode MIME header string.
     * @param string $string The string.
     * @param string $charset The charset.
     * @return string The encoded string.
     */
    protected static function encodeForHeader(string $string, string $charset): string
    {
        return mb_encode_mimeheader($string, $charset);
    }

    /**
     * Convert encoding.
     * @param string $string The string.
     * @param string $charsetTo The charset to convert to.
     * @param string|null $charsetFrom The charset to convert from.
     * @return string The encoded string.
     */
    protected static function encodeString(string $string, string $charsetTo, string|null $charsetFrom = null): string
    {
        if ($charsetFrom === $charsetTo) {
            return $string;
        }

        return mb_convert_encoding($string, $charsetTo, $charsetFrom);
    }

    /**
     * Parse email addresses.
     * @param string|array $emails The email addresses.
     * @return array The parsed email addresses.
     */
    protected static function parseEmails(string|array $emails): array
    {
        if (is_string($emails)) {
            $emails = [$emails];
        }

        $results = [];
        foreach ($emails AS $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
                $value = null;
            }

            $key = static::validateEmail($key);

            if (!$key) {
                continue;
            }

            $results[$key] = $value ?? $key;
        }

        return $results;
    }

    /**
     * Encode, wrap and split body text into lines.
     * @param string $content The body content.
     * @param string $charset The charset.
     * @return array The body text lines.
     */
    protected static function prepareBody(string $content, string $charset): array
    {
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $content = static::encodeString($content, $charset, Mailer::getAppCharset());
        $content = static::wrap($content);
        $content = implode("\n", $content);
        $content = rtrim($content, "\n");

        return explode("\n", $content);
    }

    /**
     * Generate a random string.
     * @return string The random string.
     */
    protected static function randomString(): string
    {
        return md5(random_bytes(16));
    }

    /**
     * Validate an email address.
     * @param string $email The email address.
     * @return string|null The validated email address.
     */
    protected static function validateEmail(string $email): string|null
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Wrap a string to a character limit.
     * @param string $string The string.
     * @param int $charLimit The character limit.
     * @return array The wrapped lines.
     */
    protected static function wrap(string $string, int $charLimit = 998): array
    {
        $string = str_replace(["\r\n", "\r"], "\n", $string);
        $lines = explode("\n", $string);

        $formatted = [];
        foreach ($lines AS $line) {
            if (!$line && $line !== '0') {
                $formatted[] = '';
                continue;
            }

            if (strlen($line) <= $charLimit) {
                $formatted[] = $line;
                continue;
            }

            $parts = preg_split('/(<[^>]*>)/', $line, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

            $currentLine = '';
            foreach ($parts AS $part) {
                $currentLine ??= '';
                $partLength = strlen($part);

                // if current line will remain below length limit
                if (strlen($currentLine) + $partLength <= $charLimit) {
                    $currentLine .= $part;
                    continue;
                }

                // if html tag, wordwrap the whole line
                if ($part[0] === '<' && $part[$partLength - 1] === '>') {
                    $formatted[] = $currentLine;
                    if ($partLength <= $charLimit) {
                        $currentLine = $part;
                    } else {
                        $formatted[] = $part;
                        $currentLine = null;
                    }

                    continue;
                }

                // wordwrap the line
                $formatted[] = $currentLine;
                $wrapped = wordwrap($part, $charLimit);
                $wrappedLines = explode("\n", $wrapped);
                $currentLine = array_pop($wrappedLines);
                $formatted = array_merge($formatted, $wrappedLines);
            }

            if ($currentLine !== null) {
                $formatted[] = $currentLine;
            }
        }

        return $formatted;
    }

}
