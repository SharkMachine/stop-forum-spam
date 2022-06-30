<?php

declare(strict_types=1);

namespace SharkMachine\Lib\StopForumSpan\Exception;

use Exception;

final class UnableToParseResponseException extends AbstractException
{
    private const MESSAGE = 'Unable to parse DateTime: %s';

    /**
     * @param Exception $previous
     *
     * @return static
     */
    public static function createFromException(Exception $previous): self
    {
        return new self(sprintf(self::MESSAGE, $previous->getMessage()), 0, $previous);
    }
}
