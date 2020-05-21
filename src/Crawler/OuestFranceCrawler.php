<?php

declare(strict_types=1);

namespace Crawler;

use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;

class OuestFranceCrawler extends BaseCrawler implements AdCrawlerInterface
{
    private const WEBSITE_ORIGIN = 'Ouest France';
    private const WEBSITE_BASE_URL = 'https://www.ouestfrance-immo.com';

    public function crawl(): void
    {
        foreach ($this->urls as $city => $url) {
            $crawler = $this->getCrawlerForUrl($url);

            $crawler->filter('#listAnnonces>a')->each(function (Crawler $adCrawler) use ($city) {
                $ad = Ad::create(
                    self::WEBSITE_ORIGIN,
                    $this->extractAdId($adCrawler),
                    $this->extractAdUrl($adCrawler),
                    $this->extractAdMainPicture($adCrawler),
                    $this->extractAdPrice($adCrawler),
                    $this->extractAdCity($adCrawler, $city),
                    $this->extractAdAddress($adCrawler),
                    $this->extractAdTitle($adCrawler),
                    $this->extractAdDescription($adCrawler),
                    $this->extractAdCriterias($adCrawler),
                    $this->extractAdPublicationDate($adCrawler)
                );

                if (!$this->database->exists($ad)) {
                    $this->database->insert($ad);
                    $this->notificationManager->notify($ad);
                }
            });
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

    public function extractAdMainPicture(Crawler $adCrawler): string
    {
        return $adCrawler->filter('div.photoClassique img')->attr('data-original');
    }

    public function extractAdPrice(Crawler $adCrawler): ?int
    {
        $selector = 'div.annBlocDesc span.annPrix';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        preg_match('/(\d{1,})/', $adCrawler->filter($selector)->text(), $matches);

        return (int) $matches[1];
    }

    public function extractAdTitle(Crawler $adCrawler): ?string
    {
        $selector = 'div.annBlocDesc span.annTitre';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    public function extractAdCity(Crawler $adCrawler, string $city = null): ?string
    {
        $selector = 'div.annBlocDesc span.annVille';

        if (0 === $adCrawler->filter($selector)->count()) {
            return $city;
        }

        return $adCrawler->filter($selector)->text();
    }

    public function extractAdAddress(Crawler $adCrawler): ?string
    {
        $selector = 'div.annBlocDesc span.annAdresse';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    public function extractAdDescription(Crawler $adCrawler): ?string
    {
        $selector = 'div.annBlocDesc span.annTexte';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    public function extractAdCriterias(Crawler $adCrawler): ?string
    {
        $selector = 'div.annBlocDesc span.annCriteres';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    public function extractAdPublicationDate(Crawler $adCrawler): ?\DateTime
    {
        $date = $adCrawler->filter('div.annBlocDesc span.annDebAff')->text();
        preg_match('/(\d{2})\/(\d{2})\/(\d{2})/', $date, $matches);

        if (!isset($matches[1]) | !isset($matches[2]) | !isset($matches[3])) {
            return null;
        }

        return new \DateTime(sprintf('20%d-%d-%d', $matches[3], $matches[2], $matches[1]));
    }
}
