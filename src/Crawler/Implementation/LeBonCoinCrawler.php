<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Symfony\Component\DomCrawler\Crawler;

class LeBonCoinCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function extractAdId(Crawler $adCrawler): string
    {
        preg_match('/locations\/(.*)\.htm/', $adCrawler->filter('a')->attr('href'), $matches);

        return $matches[1];
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', $this->baseUrl, $adCrawler->filter('a')->attr('href'));
    }
}
