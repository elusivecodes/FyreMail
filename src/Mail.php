<?php
declare(strict_types=1);

namespace Fyre\Mail;

use Fyre\Mail\Exceptions\MailException;

use function array_key_exists;
use function array_search;
use function class_exists;
use function is_array;

/**
 * Mail
 */
abstract class Mail
{

    public const DEFAULT = 'default';

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
     * Determine if a config exists.
     * @param string $key The config key.
     * @return bool TRUE if the config exists, otherwise FALSE.
     */
    public static function hasConfig(string $key = self::DEFAULT): bool
    {
        return array_key_exists($key, static::$config);
    }

    /**
     * Determine if a handler is loaded.
     * @param string $key The config key.
     * @return bool TRUE if the handler is loaded, otherwise FALSE.
     */
    public static function isLoaded(string $key = self::DEFAULT): bool
    {
        return array_key_exists($key, static::$instances);
    }

    /**
     * Load a handler.
     * @param array $options Options for the handler.
     * @return Mailer The handler.
     * @throws MailException if the handler is not valid.
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
     * @throws MailException if the config is not valid.
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
     * @return bool TRUE if the handler was removed, otherwise FALSE.
     */
    public static function unload(string $key = self::DEFAULT): bool
    {
        if (!array_key_exists($key, static::$config)) {
            return false;
        }

        unset(static::$instances[$key]);
        unset(static::$config[$key]);

        return true;
    }

    /**
     * Load a shared handler instance.
     * @param string $key The config key.
     * @return Mailer The handler.
     */
    public static function use(string $key = self::DEFAULT): Mailer
    {
        return static::$instances[$key] ??= static::load(static::$config[$key] ?? []);
    }

}
