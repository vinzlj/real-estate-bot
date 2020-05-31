<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Symfony\Component\DomCrawler\Crawler;

class GuyHoquetCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function getCrawlerForUrl(string $url): Crawler
    {
        $response = $this->client->request('GET', $url, [
            'verify_peer' => false,
            'headers' => $this->getRequestHeaders(),
        ]);

        $content = json_decode($response->getContent(), true);
        $content = $content['templates']['properties'];

        $this->saveResponse($url, $content);

        return new Crawler($content);
    }

    public function extractAdId(Crawler $adCrawler): string
    {
        preg_match('/property_link_block_(\d{3,})/', $adCrawler->filter('a')->attr('id'), $matches);

        return $matches[1];
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return $adCrawler->filter('a')->attr('href');
    }

    protected function getRequestHeaders(): array
    {
        return array_merge(parent::getRequestHeaders(), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }
}
