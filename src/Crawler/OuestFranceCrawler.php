<?php

declare(strict_types=1);

namespace Crawler;

use Symfony\Component\DomCrawler\Crawler;

class OuestFranceCrawler extends BaseCrawler implements AdCrawlerInterface
{
    private const WEBSITE_BASE_URL = 'https://www.ouestfrance-immo.com';

    protected $websiteOrigin = 'Ouest France';
    protected $adSelector = '#listAnnonces>a';

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

    public function extractAdId(Crawler $adCrawler): int
    {
        return (int) $adCrawler->filter('div')->attr('data-id');
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', self::WEBSITE_BASE_URL, $adCrawler->attr('href'));
    }
}
