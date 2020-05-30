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
        $response = $this->client->request('GET', $url, [
            'verify_peer' => false,
            'headers' => [
                'pragma' => 'no-cache',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
                'cache-control' => 'no-cache',
                'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36',
            ]
        ]);

        return new Crawler($response->getContent());
    }
}
