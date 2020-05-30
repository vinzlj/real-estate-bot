<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;

class FnaimCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function crawlUrl(Crawler $crawler): void
    {
        $crawler->filter($this->adSelector)->each(function (Crawler $adCrawler) {
            if (0 === $adCrawler->filter('a.linkAnnonce')->count()) {
                return;
            }

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

    public function extractAdId(Crawler $adCrawler): int
    {
        return (int) $adCrawler->filter('a.linkAnnonce')->attr('data-id');
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', $this->baseUrl, $adCrawler->filter('a.linkAnnonce')->attr('href'));
    }
}
