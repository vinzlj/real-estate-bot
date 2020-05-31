<?php

declare(strict_types=1);

namespace Event;

use Symfony\Contracts\EventDispatcher\Event;

class CrawlingUrlEvent extends Event
{
    public const NAME = 'crawler.running';

    private const TERMINAL_MAX_WIDTH = 120;

    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getFormattedUrl(): string
    {
        if (self::TERMINAL_MAX_WIDTH < strlen($this->url)) {
            return sprintf('%s...', substr($this->url, 0, self::TERMINAL_MAX_WIDTH));
        }

        return $this->url;
    }
}
