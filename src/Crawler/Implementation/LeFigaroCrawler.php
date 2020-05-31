<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Symfony\Component\DomCrawler\Crawler;

class LeFigaroCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function extractAdId(Crawler $adCrawler): string
    {
        return $adCrawler->attr('data-classified-id');
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', $this->baseUrl, $adCrawler->filter('h2.item-type a')->attr('href'));
    }
}
