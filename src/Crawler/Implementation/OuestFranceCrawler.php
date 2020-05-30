<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Symfony\Component\DomCrawler\Crawler;

class OuestFranceCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function crawl(): void
    {
        foreach ($this->urls as $url) {
            $crawler = $this->getCrawlerForUrl($url);

            if (0 < $crawler->filter('div.noAnnonces')->count()) {
                continue;
            }

            $this->crawlUrl($crawler);
        }
    }

    public function extractAdId(Crawler $adCrawler): string
    {
        return $adCrawler->filter('div')->attr('data-id');
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', $this->baseUrl, $adCrawler->attr('href'));
    }
}
