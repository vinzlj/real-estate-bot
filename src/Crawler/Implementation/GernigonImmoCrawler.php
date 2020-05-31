<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;

class GernigonImmoCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function crawl(): void
    {
        foreach ($this->urls as $url) {
            $this->crawlUrlWithPost($url);
        }
    }

    public function crawlUrlWithPost(string $url): void
    {
        preg_match('/(https:\/\/.*)\/(\d{3,})/', $url, $matches);

        $response = $this->client->request('POST', $matches[1].'/', [
            'verify_peer' => false,
            'headers' => $this->getRequestHeaders(),
            'body' => [
                'nature' => 2, // location
                'city[]' => $matches[2],
                'type[]' => 2, // maison
                'price' => '0000000500-0000001100',
                'currency' => 'EUR',
            ],
        ]);

        $content = $response->getContent();

        $this->saveResponse($url, $content);

        $crawler = new Crawler($content);

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

    public function extractAdId(Crawler $adCrawler): string
    {
        return substr(base64_encode($this->extractAdUrl($adCrawler)), -20, 20);
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', $this->baseUrl, $adCrawler->filter('a')->attr('href'));
    }
}
