<?php

declare(strict_types=1);

namespace Crawler;

use Database\DatabaseInterface;
use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BaseCrawler implements CrawlerInterface
{
    protected $client;
    protected $database;

    protected $adSelector;
    protected $name;
    protected $baseUrl;
    protected $urls;

    public function __construct(
        HttpClientInterface $client,
        DatabaseInterface $database,
        array $configuration
    ) {
        $this->client = $client;
        $this->database = $database;
        $this->adSelector = $configuration['ad_selector'];
        $this->name = $configuration['name'];
        $this->baseUrl = $configuration['base_url'] ?? null;
        $this->urls = $configuration['urls'];
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
                $this->name,
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
            'headers' => $this->getRequestHeaders(),
        ]);

        $content = $response->getContent();

        $this->saveResponse($content);

        return new Crawler($content);
    }

    protected function getRequestHeaders(): array
    {
        return [];
    }

    private function saveResponse(string $content): void
    {
        file_put_contents(sprintf(__DIR__.'/../../data/%s.html', strtolower(str_replace(' ', '_', $this->name))), $content);
    }
}
