<?php

declare(strict_types=1);

namespace Crawler;

use Database\DatabaseInterface;
use Exception;
use Notification\NotificationManager;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseCrawler implements CrawlerInterface
{
    protected $client;
    protected $database;
    protected $notificationManager;
    protected $urls;

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

    public function display(): void
    {
        dump($this->database->getAds());
    }

    public function getCrawlerForUrl(string $url): Crawler
    {
        $response = $this->client->request('GET', $url);

        return new Crawler($response->getContent());
    }

    public function crawl(): void
    {
        throw new Exception('This method should be implemented in child crawler.');
    }
}
