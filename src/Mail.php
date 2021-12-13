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
     * @param array $config Options for the handler.
     * @return Mailer The handler.
     * @throws MailException if the handler is invalid.
     */
    public static function load(array $config = []): Mailer
    {
        if (!array_key_exists('className', $config)) {
            throw MailException::forInvalidClass();
        }

        if (!class_exists($config['className'], true)) {
            throw MailException::forInvalidClass($config['className']);
        }

        return new $config['className']($config);
    }

    /**
     * Set handler config.
     * @param string $key The config key.
     * @param array $config The config options.
     */
    public static function setConfig(string $key, array $config): void
    {
        static::$config[$key] = $config;
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
