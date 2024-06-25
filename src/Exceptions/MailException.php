<?php
declare(strict_types=1);

namespace Fyre\Mail\Exceptions;

use RuntimeException;

/**
 * MailException
 */
class MailException extends RuntimeException
{
    public static function forConfigExists(string $key): static
    {
        return new static('Mail handler config already exists: '.$key);
    }

    public static function forDeliveryFailed(string $message = ''): static
    {
        return new static('Mail handler delivery failed: '.$message);
    }

    public static function forInvalidAttachment(string $filename = ''): static
    {
        return new static('Mail handler invalid attachment: '.$filename);
    }

    public static function forInvalidClass(string $className = ''): static
    {
        return new static('Mail handler class not found: '.$className);
    }

    public static function forInvalidConfig(string $key): static
    {
        return new static('Mail handler invalid config: '.$key);
    }

    public static function forInvalidFormat(string $format = ''): static
    {
        return new static('Invalid email format: '.$format);
    }

    public static function forMissingRecipients(): static
    {
        return new static('Mail handler missing recipients');
    }
}
