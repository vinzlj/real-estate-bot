<?php

declare(strict_types=1);

namespace Crawler;

use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;

class Century21Crawler extends BaseCrawler implements AdCrawlerInterface
{
    private const WEBSITE_ORIGIN = 'Century 21';
    private const WEBSITE_BASE_URL = 'https://www.century21byouestsaintseb.com';

    public function crawl(): void
    {
        foreach ($this->urls as $url) {
            $crawler = $this->getCrawlerForUrl($url);

            $crawler->filter('#blocANNONCES li.annonce')->each(function (Crawler $adCrawler) {
                $ad = Ad::create(
                    self::WEBSITE_ORIGIN,
                    $this->extractAdId($adCrawler),
                    $this->extractAdUrl($adCrawler),
                    $this->extractAdMainPicture($adCrawler),
                    $this->extractAdPrice($adCrawler),
                    $this->extractAdCity($adCrawler),
                    $this->extractAdAddress($adCrawler),
                    $this->extractAdTitle($adCrawler),
                    $this->extractAdDescription($adCrawler),
                    $this->extractAdCriterias($adCrawler),
                    $this->extractAdPublicationDate($adCrawler)
                );

                if (!$this->database->exists($ad)) {
                    $this->database->insert($ad);
                }
            });
        }
    }

    public function extractAdId(Crawler $adCrawler): int
    {
        return (int) $adCrawler->filter('div')->attr('data-uid');
    }

    public function extractAdUrl(Crawler $adCrawler): ?string
    {
        $selector = 'div.zone-text-loupe a';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return sprintf('%s%s', self::WEBSITE_BASE_URL, $adCrawler->filter($selector)->attr('href'));
    }

    public function extractAdMainPicture(Crawler $adCrawler): ?string
    {
        return sprintf('%s%s', self::WEBSITE_BASE_URL, $adCrawler->filter('p.photoAnnonce img')->attr('src'));
    }

    public function extractAdPrice(Crawler $adCrawler): ?int
    {
        $selector = 'div.price';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        preg_match('/(\d{1,} ?\d{1,})/', $adCrawler->filter($selector)->text(), $matches);

        return (int) str_replace(' ', '', $matches[1]);
    }

    public function extractAdTitle(Crawler $adCrawler): ?string
    {
        return null;
    }

    public function extractAdCity(Crawler $adCrawler): ?string
    {
        $selector = 'div.zone-text-loupe h3';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        preg_match('/([a-zA-Z ]*)/', $adCrawler->filter($selector)->text(), $matches);

        if (2 > count($matches)) {
            return null;
        }

        return $matches[1];
    }

    public function extractAdAddress(Crawler $adCrawler): ?string
    {
        $selector = 'div.zone-text-loupe h3';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        preg_match('/(\d{1,})/', $adCrawler->filter($selector)->text(), $matches);

        if (2 > count($matches)) {
            return null;
        }

        return $matches[1];
    }

    public function extractAdDescription(Crawler $adCrawler): ?string
    {
        return null;
    }

    public function extractAdCriterias(Crawler $adCrawler): ?string
    {
        $selector = 'div.zone-text-loupe h4.detail_vignette';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    public function extractAdPublicationDate(Crawler $adCrawler): ?\DateTime
    {
        return null;
    }
}
