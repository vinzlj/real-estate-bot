<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Symfony\Component\DomCrawler\Crawler;

class Century21SaintSebastienCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function extractAdId(Crawler $adCrawler): int
    {
        return (int) $adCrawler->filter('div')->attr('data-uid');
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', $this->baseUrl, $adCrawler->filter('div.zone-text-loupe a')->attr('href'));
    }
}
