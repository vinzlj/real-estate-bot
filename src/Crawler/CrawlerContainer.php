<?php

declare(strict_types=1);

namespace Crawler;

use Database\DatabaseInterface;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlerContainer
{
    /** @var CrawlerInterface[] */
    private $crawlers = [];

    public function __construct(
        array $configuration,
        HttpClientInterface $client,
        DatabaseInterface $database
    ) {
        foreach ($configuration as $crawlerName => $crawlerConfiguration) {
            $crawlerClass = $this->getCrawlerClass($crawlerName);

            if (!class_exists($crawlerClass)) {
                throw new Exception(sprintf('No crawler found for: %s', $crawlerClass));
            }

            /** @var BaseCrawler */
            $this->crawlers[] = new $crawlerClass(
                $client,
                $database,
                $crawlerConfiguration
            );
        }
    }

    public function crawl(): void
    {
        foreach ($this->crawlers as $crawler) {
            $crawler->crawl();
        };
    }

    private function getCrawlerClass(string $name): string
    {
        return sprintf('Crawler\Implementation\%sCrawler', str_replace('_', '', ucwords($name, '_')));
    }
}
