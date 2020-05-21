<?php

namespace Crawler;

use Symfony\Component\DomCrawler\Crawler;

interface CrawlerInterface
{
    public function crawl(): void;
    public function display(): void;
    public function getCrawlerForUrl(string $url): Crawler;
}
