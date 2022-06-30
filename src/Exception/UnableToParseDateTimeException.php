<?php

declare(strict_types=1);

namespace SharkMachine\Lib\StopForumSpan\Exception;

use Exception;

use function sprintf;

final class UnableToParseDateTimeException extends AbstractException
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
