<?php
declare(strict_types=1);

namespace Fyre\Mail;

use Fyre\Mail\Exceptions\MailException;

use function array_key_exists;
use function array_replace;
use function php_uname;

/**
 * Mailer
 */
abstract class Mailer
{
    protected static array $defaults = [
        'appCharset' => null,
        'charset' => 'utf-8',
        'client' => null,
    ];

    protected array $config;

    protected MailManager $mailManager;

    /**
     * New Cacher constructor.
     *
     * @param array $options Options for the handler.
     */
    public function __construct(array $options = [])
    {
        $this->config = array_replace(self::$defaults, static::$defaults, $options);
    }

    /**
     * Create a new Email.
     *
     * @return Email The new Email.
     */
    public function email(): Email
    {
        return new Email($this);
    }

    /**
     * Get the client hostname.
     *
     * @return string The client hostname.
     */
    public function getClient(): string
    {
        if ($this->config['client']) {
            return $this->config['client'];
        }

        if (array_key_exists('SERVER_NAME', $_SERVER)) {
            return $_SERVER['SERVER_NAME'];
        }

        if (array_key_exists('SERVER_ADDR', $_SERVER)) {
            return '['.$_SERVER['SERVER_ADDR'].']';
        }

        return php_uname('n');
    }

    /**
     * Get the config.
     *
     * @return array The config.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Send an email.
     *
     * @param Email $email The email to send.
     */
    abstract public function send(Email $email): void;

    /**
     * Check an email has recipients.
     *
     * @param Email $email The email to check.
     *
     * @throws MailException if the email has no recipients.
     */
    protected static function checkEmail(Email $email): void
    {
        if ($email->getRecipients() === []) {
            throw MailException::forMissingRecipients();
        }
    }
}
