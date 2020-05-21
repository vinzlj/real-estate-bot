<?php

declare(strict_types=1);

namespace Crawler;

use Database\DatabaseInterface;
use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OuestFranceCrawler implements CrawlerInterface
{
    private $client;
    private $database;
    private $urls;

    public function __construct(HttpClientInterface $client, DatabaseInterface $database, array $urls)
    {
        $this->client = $client;
        $this->database = $database;
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

                if (!$this->database->exists($ad)) {
                    $this->database->insert($ad);
                }
            });
        }
    }

    public function display(): void
    {
        dump($this->database->getAds());
    }

    public function getCrawlerForUrl(string $url): Crawler
    {
        $response = $this->client->request('GET', $url);

        return new Crawler($response->getContent());
    }

    public function extractAdId(Crawler $adCrawler): int
    {
        return (int) $adCrawler->filter('div')->attr('data-id');
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return $adCrawler->attr('href');
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
            return null;
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
