<?php
declare(strict_types=1);

namespace Fyre\Mail;

use
    Fyre\Mail\Exceptions\MailException;

use function
    array_key_exists,
    class_exists;

/**
 * Mail
 */
abstract class Mail
{

    protected static array $config = [];

    protected static array $instances = [];

    /**
     * Clear instances.
     */
    public static function clear(): void
    {
        static::$instances = [];
    }

    /**
     * Load a handler.
     * @param array $options Options for the handler.
     * @return Mailer The handler.
     * @throws MailException if the handler is invalid.
     */
    public static function load(array $options = []): Mailer
    {
        if (!array_key_exists('className', $options)) {
            throw MailException::forInvalidClass();
        }

        if (!class_exists($options['className'], true)) {
            throw MailException::forInvalidClass($options['className']);
        }

        return new $options['className']($options);
    }

    /**
     * Set handler config.
     * @param string $key The config key.
     * @param array $options The config options.
     */
    public static function setConfig(string $key, array $options): void
    {
        static::$config[$key] = $options;
    }

    /**
     * Load a shared handler instance.
     * @param string $key The config key.
     * @return Mailer The handler.
     */
    public static function use(string $key = 'default'): Mailer
    {
        return static::$instances[$key] ??= static::load(static::$config[$key] ?? []);
    }

}
