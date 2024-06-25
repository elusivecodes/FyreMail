<?php
declare(strict_types=1);

namespace Fyre\Mail\Exceptions;

use RuntimeException;

/**
 * SmtpException
 */
class SmtpException extends RuntimeException
{
    public static function forAuthFailed(): static
    {
        return new static('Mail handler authentication failed');
    }

    public static function forConnectionFailed(): static
    {
        return new static('Mail handler connection failed');
    }

    public static function forInvalidData(): static
    {
        return new static('Mail handler invalid data');
    }

    public static function forInvalidResponse(): static
    {
        return new static('Mail handler invalid response');
    }
}
