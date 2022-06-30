<?php

declare(strict_types=1);

namespace SharkMachine\Lib\StopForumSpan;

use DateTimeImmutable;
use Exception;
use SharkMachine\Lib\StopForumSpan\Exception\UnableToParseDateTimeException;

class Response
{
    /**
     * @var string
     */
    private string $type;

    /**
     * @var bool
     */
    private bool $appears;

    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $lastSeen;

    /**
     * @var int
     */
    private int $frequency;

    /**
     * @param string $type
     * @param bool   $appears
     * @param string $lastSeen
     * @param int    $frequency
     *
     * @throws UnableToParseDateTimeException
     */
    public function __construct(string $type, bool $appears, string $lastSeen, int $frequency)
    {
        $this->type      = $type;
        $this->appears   = $appears;
        try {
            $this->lastSeen = new DateTimeImmutable($lastSeen);
        } catch (Exception $ex) {
            throw UnableToParseDateTimeException::createFromException($ex);
        }
        $this->frequency = $frequency;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isAppears(): bool
    {
        return $this->appears;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getLastSeen(): DateTimeImmutable
    {
        return $this->lastSeen;
    }

    /**
     * @return int
     */
    public function getFrequency(): int
    {
        return $this->frequency;
    }
}
