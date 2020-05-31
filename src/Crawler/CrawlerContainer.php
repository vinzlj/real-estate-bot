<?php

declare(strict_types=1);

namespace Crawler;

use Database\DatabaseInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CrawlerContainer
{
    /** @var CrawlerInterface[] */
    private $crawlers = [];
    private $configuration;

    public function __construct(
        array $configuration,
        HttpClientInterface $client,
        DatabaseInterface $database,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->configuration = $configuration;

        foreach ($this->configuration as $crawlerName => $crawlerConfiguration) {
            $crawlerClass = $this->getCrawlerClass($crawlerName);

            if (!class_exists($crawlerClass)) {
                throw new Exception(sprintf('No crawler found for: %s', $crawlerClass));
            }

            /** @var BaseCrawler */
            $this->crawlers[] = new $crawlerClass(
                $eventDispatcher,
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

    public function getTotalNumberOfUrls(): int
    {
        $numberOfUrls = 0;

        array_walk($this->configuration, function (array $crawlerConfiguration) use (&$numberOfUrls) {
            $numberOfUrls += count($crawlerConfiguration['urls']);
        });

        return $numberOfUrls;
    }

    private function getCrawlerClass(string $name): string
    {
        return sprintf('Crawler\Implementation\%sCrawler', str_replace('_', '', ucwords($name, '_')));
    }
}
