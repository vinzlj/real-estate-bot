<?php

declare(strict_types=1);

namespace Crawler;

use Symfony\Component\DomCrawler\Crawler;

class SeLogerCrawler extends BaseCrawler implements AdCrawlerInterface
{
    protected $websiteOrigin = 'Se Loger';
    protected $adSelector = 'div[class*=\'ListContent__SmartClassifiedExtended\']';

    public function extractAdId(Crawler $adCrawler): int
    {
        preg_match('/\/(\d{5,})\.htm/', $this->extractAdUrl($adCrawler), $matches);

        return (int) $matches[1];
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        $selector = 'a[class*=\'CoveringLink-\']';

        preg_match('/(.*\.htm)/', $adCrawler->filter($selector)->attr('href'), $matches);

        return $matches[1];
    }
}
