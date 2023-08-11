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

    protected static string|null $appCharset = null;

    protected static array $defaults = [
        'charset' => 'utf-8',
        'client' => null
    ];

    protected array $config;

    /**
     * New Cacher constructor.
     * @param array $options Options for the handler.
     */
    public function __construct(array $options = [])
    {
        $this->config = array_replace(self::$defaults, static::$defaults, $options);
    }

    /**
     * Create a new Email.
     * @return Email The new Email.
     */
    public function email(): Email
    {
        return new Email($this);
    }

    /**
     * Get the charset.
     * @return string The charset.
     */
    public function getCharset(): string
    {
        return $this->config['charset'];
    }

    /**
     * Get the client hostname.
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
     * Send an email.
     * @param Email $email The email to send.
     */
    abstract public function send(Email $email): void;

    /**
     * Get the app charset.
     * @return string|null The app charset.
     */
    public static function getAppCharset(): string|null
    {
        return static::$appCharset;
    }

    /**
     * Set the app charset.
     * @param string|null $charset The charset.
     */
    public static function setAppCharset(string|null $charset = null): void
    {
        static::$appCharset = $charset;
    }

    /**
     * Check an email has recipients.
     * @param Email $email The email to check.
     * @throws MailException if the email has no recipients.
     */
    protected static function checkEmail(Email $email): void
    {
        if ($email->getRecipients() === []) {
            throw MailException::forMissingRecipients();
        }
    }

}
