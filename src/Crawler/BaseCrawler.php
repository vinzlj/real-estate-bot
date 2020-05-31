<?php

declare(strict_types=1);

namespace Crawler;

use Database\DatabaseInterface;
use Event\CrawlingUrlEvent;
use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BaseCrawler implements CrawlerInterface
{
    protected $eventDispatcher;
    protected $client;
    protected $database;

    protected $adSelector;
    protected $name;
    protected $baseUrl;
    protected $urls;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        HttpClientInterface $client,
        DatabaseInterface $database,
        array $configuration
    ) {
        $this->eventDispatcher = $eventDispatcher;
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
            $this->eventDispatcher->dispatch(new CrawlingUrlEvent($url), CrawlingUrlEvent::NAME);

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

        $this->saveResponse($url, $content);

        return new Crawler($content);
    }

    protected function getRequestHeaders(): array
    {
        return [
            'pragma' => 'no-cache',
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
            'cache-control' => 'no-cache',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36',
        ];
    }

    protected function saveResponse(string $url, string $content): void
    {
        preg_match('/\/\/(.*)(\?|\/)/', $url, $matches);

        file_put_contents(sprintf(__DIR__.'/../../data/%s.html', str_replace('/', '_', $matches[1])), $content);
    }
}
