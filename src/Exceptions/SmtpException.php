<?php
declare(strict_types=1);

namespace Fyre\Mail\Exceptions;

use
    RuntimeException;

/**
 * SmtpException
 */
class SmtpException extends RuntimeException
{

    public static function forAuthFailed()
    {
        return new static('Mail handler authentication failed');
    }

    public static function forConnectionFailed()
    {
        return new static('Mail handler connection failed');
    }

    public static function forInvalidData()
    {
        return new static('Mail handler invalid data');
    }

    public static function forInvalidResponse()
    {
        return new static('Mail handler invalid response');
    }

}
