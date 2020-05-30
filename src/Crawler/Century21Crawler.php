<?php

declare(strict_types=1);

namespace Crawler;

use Symfony\Component\DomCrawler\Crawler;

class Century21Crawler extends BaseCrawler implements AdCrawlerInterface
{
    private const WEBSITE_BASE_URL = 'https://www.century21byouestsaintseb.com';

    protected $websiteOrigin = 'Century 21';
    protected $adSelector = '#blocANNONCES li.annonce';

    public function extractAdId(Crawler $adCrawler): int
    {
        return (int) $adCrawler->filter('div')->attr('data-uid');
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', self::WEBSITE_BASE_URL, $adCrawler->filter('div.zone-text-loupe a')->attr('href'));
    }
}
