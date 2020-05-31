<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Symfony\Component\DomCrawler\Crawler;

class AgencesReuniesOuestCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function extractAdId(Crawler $adCrawler): string
    {
        preg_match('/fiches\/(.*)\//', $this->extractAdUrl($adCrawler), $matches);

        return $matches[1];
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', $this->baseUrl, str_replace('..', '', $adCrawler->filter('a')->attr('href')));
    }
}
