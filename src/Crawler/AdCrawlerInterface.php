<?php

namespace Crawler;

use Symfony\Component\DomCrawler\Crawler;

interface AdCrawlerInterface
{
    public function extractAdId(Crawler $adCrawler): int;
    public function extractAdUrl(Crawler $adCrawler): string;
}
