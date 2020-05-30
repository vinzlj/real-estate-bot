<?php

declare(strict_types=1);

namespace Crawler;

use Database\DatabaseInterface;
use Model\Ad;
use Notification\NotificationManager;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseCrawler implements CrawlerInterface
{
    protected $client;
    protected $database;
    protected $notificationManager;
    protected $urls;

    protected $websiteOrigin;
    protected $adSelector;

    public function __construct(
        HttpClientInterface $client,
        DatabaseInterface $database,
        NotificationManager $notificationManager,
        array $urls
    ) {
        $this->client = $client;
        $this->database = $database;
        $this->notificationManager = $notificationManager;
        $this->urls = $urls;
    }

    public function crawl(): void
    {
        foreach ($this->urls as $url) {
            $crawler = $this->getCrawlerForUrl($url);

            $this->crawlUrl($crawler);
        }
    }

    public function crawlUrl(Crawler $crawler): void
    {
        $crawler->filter($this->adSelector)->each(function (Crawler $adCrawler) {
            $ad = Ad::create(
                $this->websiteOrigin,
                $this->extractAdId($adCrawler),
                $this->extractAdUrl($adCrawler)
            );

            if (!$this->database->exists($ad)) {
                $this->database->insert($ad);
            }
        });
    }

    public function getCrawlerForUrl(string $url): Crawler
    {
        $this->getRequestHeaders();

        $response = $this->client->request('GET', $url, [
            'verify_peer' => false,
            'headers' => $this->getRequestHeaders(),
        ]);

        $content = $response->getContent();

        file_put_contents(sprintf(__DIR__.'/../../data/%s.html', strtolower(str_replace(' ', '_', $this->websiteOrigin))), $content);

        return new Crawler($content);
    }

    protected function getRequestHeaders(): array
    {
        return [];
    }
}
