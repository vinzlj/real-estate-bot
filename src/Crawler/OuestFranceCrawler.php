<?php

declare(strict_types=1);

namespace Crawler;

use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OuestFranceCrawler
{
    private $client;
    private $urls;
    private $ads = [];

    public function __construct(HttpClientInterface $client, array $urls)
    {
        $this->client = $client;
        $this->urls = $urls;
    }

    public function crawl(): void
    {
        foreach ($this->urls as $city => $url) {
            $crawler = $this->getCrawlerForUrl($url);

            $crawler->filter('#listAnnonces>a')->each(function (Crawler $adCrawler) use ($city) {
                $ad = new Ad();
                $ad->setId($this->extractAdId($adCrawler));
                $ad->setUrl($this->extractAdUrl($adCrawler));
                $ad->setMainPicture($this->extractAdMainPicture($adCrawler));
                $ad->setPrice($this->extractAdPrice($adCrawler));
                $ad->setTitle($this->extractAdTitle($adCrawler));
                $ad->setCity($this->extractAdCity($adCrawler, $city));
                $ad->setAddress($this->extractAdAddress($adCrawler));
                $ad->setDescription($this->extractAdDescription($adCrawler));
                $ad->setCriterias($this->extractAdCriterias($adCrawler));
                $ad->setPublicationDate($this->extractAdPublicationDate($adCrawler));

                $this->ads[$ad->id] = $ad;
            });
        }
    }

    public function display(): void
    {
        dump($this->ads);
    }

    private function getCrawlerForUrl(string $url): Crawler
    {
        $response = $this->client->request('GET', $url);

        return new Crawler($response->getContent());
    }

    private function extractAdId(Crawler $adCrawler): int
    {
        return (int) $adCrawler->filter('div')->attr('data-id');
    }

    private function extractAdUrl(Crawler $adCrawler): string
    {
        return $adCrawler->attr('href');
    }

    private function extractAdMainPicture(Crawler $adCrawler): string
    {
        return $adCrawler->filter('div.photoClassique img')->attr('data-original');
    }

    private function extractAdPrice(Crawler $adCrawler): ?int
    {
        $selector = 'div.annBlocDesc span.annPrix';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        preg_match('/(\d{1,})/', $adCrawler->filter($selector)->text(), $matches);

        return (int) $matches[1];
    }

    private function extractAdTitle(Crawler $adCrawler): ?string
    {
        $selector = 'div.annBlocDesc span.annTitre';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    private function extractAdCity(Crawler $adCrawler, string $city): ?string
    {
        $selector = 'div.annBlocDesc span.annVille';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    private function extractAdAddress(Crawler $adCrawler): ?string
    {
        $selector = 'div.annBlocDesc span.annAdresse';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    private function extractAdDescription(Crawler $adCrawler): ?string
    {
        $selector = 'div.annBlocDesc span.annTexte';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    private function extractAdCriterias(Crawler $adCrawler): ?string
    {
        $selector = 'div.annBlocDesc span.annCriteres';

        if (0 === $adCrawler->filter($selector)->count()) {
            return null;
        }

        return $adCrawler->filter($selector)->text();
    }

    private function extractAdPublicationDate(Crawler $adCrawler): ?\DateTime
    {
        $date = $adCrawler->filter('div.annBlocDesc span.annDebAff')->text();
        preg_match('/(\d{2})\/(\d{2})\/(\d{2})/', $date, $matches);

        if (!isset($matches[1]) | !isset($matches[2]) | !isset($matches[3])) {
            return null;
        }

        return new \DateTime(sprintf('20%d-%d-%d', $matches[3], $matches[2], $matches[1]));
    }
}
