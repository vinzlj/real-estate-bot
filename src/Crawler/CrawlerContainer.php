<?php

declare(strict_types=1);

namespace Crawler;

class CrawlerContainer
{
    /** @var CrawlerInterface[] */
    private $crawlers = [];

    public function addCrawler(CrawlerInterface $crawler): void
    {
        $this->crawlers[] = $crawler;
    }

    public function crawl(): void
    {
        foreach ($this->crawlers as $crawler) {
            $crawler->crawl();
        };
    }
}
