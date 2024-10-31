<?php
declare(strict_types=1);

namespace Fyre\Mail;

use Fyre\Mail\Exceptions\MailException;

use function array_key_exists;
use function class_exists;
use function is_subclass_of;

/**
 * MailManager
 */
class MailManager
{
    public const DEFAULT = 'default';

    protected string|null $appCharset = null;

    protected array $config = [];

    protected array $instances = [];

    /**
     * New MailManager constructor.
     *
     * @param array $config The MailManager config.
     * @param string|null $appCharset The application character set.
     */
    public function __construct(array $config = [], string|null $appCharset = null)
    {
        $this->appCharset = $appCharset;

        foreach ($config as $key => $options) {
            $this->setConfig($key, $options);
        }
    }

    /**
     * Build a handler.
     *
     * @param array $options Options for the handler.
     * @return Mailer The handler.
     *
     * @throws MailException if the handler is not valid.
     */
    public function build(array $options = []): Mailer
    {
        if (!array_key_exists('className', $options)) {
            throw MailException::forInvalidClass();
        }

        if (!class_exists($options['className'], true) || !is_subclass_of($options['className'], Mailer::class)) {
            throw MailException::forInvalidClass($options['className']);
        }

        $options['appCharset'] ??= $this->appCharset;

        return new $options['className']($options);
    }

    /**
     * Clear all instances and configs.
     */
    public function clear(): void
    {
        $this->config = [];
        $this->instances = [];
    }

    /**
     * Get the application character set.
     *
     * @return string|null The application character set.
     */
    public function getAppCharset(): string|null
    {
        return $this->appCharset;
    }

    /**
     * Get the handler config.
     *
     * @param string|null $key The config key.
     */
    public function getConfig(string|null $key = null): array|null
    {
        if (!$key) {
            return $this->config;
        }

        return $this->config[$key] ?? null;
    }

    /**
     * Determine whether a config exists.
     *
     * @param string $key The config key.
     * @return bool TRUE if the config exists, otherwise FALSE.
     */
    public function hasConfig(string $key = self::DEFAULT): bool
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Determine whether a handler is loaded.
     *
     * @param string $key The config key.
     * @return bool TRUE if the handler is loaded, otherwise FALSE.
     */
    public function isLoaded(string $key = self::DEFAULT): bool
    {
        return array_key_exists($key, $this->instances);
    }

    /**
     * Set the application character set.
     *
     * @param string|null $appCharset The application character set.
     * @return static The MailManager.
     */
    public function setAppCharset(string|null $appCharset): static
    {
        $this->appCharset = $appCharset;

        return $this;
    }

    /**
     * Set handler config.
     *
     * @param string $key The config key.
     * @param array $options The config options.
     * @return static The MailManager.
     *
     * @throws MailException if the config is not valid.
     */
    public function setConfig(string $key, array $options): static
    {
        if (array_key_exists($key, $this->config)) {
            throw MailException::forConfigExists($key);
        }

        $this->config[$key] = $options;

        return $this;
    }

    /**
     * Unload a handler.
     *
     * @param string $key The config key.
     * @return static The MailManager.
     */
    public function unload(string $key = self::DEFAULT): static
    {
        unset($this->instances[$key]);
        unset($this->config[$key]);

        return $this;
    }

    /**
     * Load a shared handler instance.
     *
     * @param string $key The config key.
     * @return Mailer The handler.
     */
    public function use(string $key = self::DEFAULT): Mailer
    {
        return $this->instances[$key] ??= static::build($this->config[$key] ?? []);
    }
}
