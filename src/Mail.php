<?php
declare(strict_types=1);

namespace Fyre\Mail;

use
    Fyre\Mail\Exceptions\MailException;

use function
    array_key_exists,
    array_search,
    class_exists,
    is_array;

/**
 * Mail
 */
abstract class Mail
{

    protected static array $config = [];

    protected static array $instances = [];

    /**
     * Clear all instances and configs.
     */
    public static function clear(): void
    {
        static::$config = [];
        static::$instances = [];
    }

    /**
     * Get the handler config.
     * @param string|null $key The config key.
     * @return array|null
     */
    public static function getConfig(string|null $key = null): array|null
    {
        if (!$key) {
            return static::$config;
        }

        return static::$config[$key] ?? null;
    }

    /**
     * Get the key for a mailer instance.
     * @param Mailer $mailer The Mailer.
     * @return string|null The mailer key.
     */
    public static function getKey(Mailer $mailer): string|null
    {
        return array_search($mailer, static::$instances, true) ?: null;
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
     * @param string|array $key The config key.
     * @param array|null $options The config options.
     * @throws MailException if the config is invalid.
     */
    public static function setConfig(string|array $key, array|null $options = null): void
    {
        if (is_array($key)) {
            foreach ($key AS $k => $value) {
                static::setConfig($k, $value);
            }

            return;
        }

        if (!is_array($options)) {
            throw MailException::forInvalidConfig($key);
        }

        if (array_key_exists($key, static::$config)) {
            throw MailException::forConfigExists($key);
        }

        static::$config[$key] = $options;
    }

    /**
     * Unload a handler.
     * @param string $key The config key.
     */
    public static function unload(string $key = 'default'): void
    {
        unset(static::$instances[$key]);
        unset(static::$config[$key]);
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
