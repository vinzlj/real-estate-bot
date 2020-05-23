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

    public function extractAdMainPicture(Crawler $adCrawler): ?string
    {
        return null;
    }

    public function extractAdPrice(Crawler $adCrawler): ?int
    {
        $selector = 'div[class*=\'Price__Label\']';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        preg_match('/(\d{1,})/', $adCrawler->filter($selector)->text(), $matches);

        return (int) $matches[1];
    }

    public function extractAdTitle(Crawler $adCrawler): ?string
    {
        $selector = 'div[class*=\'ContentZone__Title\']';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    public function extractAdCity(Crawler $adCrawler): ?string
    {
        $selector = 'div[class*=\'ContentZone__Address\'] span';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->first()->text();
    }

    public function extractAdAddress(Crawler $adCrawler): ?string
    {
        $selector = 'div[class*=\'ContentZone__Address\'] span';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->eq(1)->text();
    }

    public function extractAdDescription(Crawler $adCrawler): ?string
    {
        return null;
    }

    public function extractAdCriterias(Crawler $adCrawler): ?string
    {
        $selector = 'ul[class*=\'ContentZone__Tags\'] li';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return implode(' / ', $adCrawler->filter($selector)->each(function (Crawler $subCrawler) {
            return $subCrawler->text();
        }));
    }

    public function extractAdPublicationDate(Crawler $adCrawler): ?\DateTime
    {
        return null;
//        $date = $adCrawler->filter('div.annBlocDesc span.annDebAff')->text();
//        preg_match('/(\d{2})\/(\d{2})\/(\d{2})/', $date, $matches);
//
//        if (!isset($matches[1]) | !isset($matches[2]) | !isset($matches[3])) {
//            return null;
//        }
//
//        return new \DateTime(sprintf('20%d-%d-%d', $matches[3], $matches[2], $matches[1]));
    }
}
