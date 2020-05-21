<?php

namespace Crawler;

use Symfony\Component\DomCrawler\Crawler;

interface AdCrawlerInterface
{
    public function extractAdId(Crawler $adCrawler): int;
    public function extractAdUrl(Crawler $adCrawler): string;
    public function extractAdMainPicture(Crawler $adCrawler): string;
    public function extractAdPrice(Crawler $adCrawler): ?int;
    public function extractAdTitle(Crawler $adCrawler): ?string;
    public function extractAdCity(Crawler $adCrawler, string $city = null): ?string;
    public function extractAdAddress(Crawler $adCrawler): ?string;
    public function extractAdDescription(Crawler $adCrawler): ?string;
    public function extractAdCriterias(Crawler $adCrawler): ?string;
    public function extractAdPublicationDate(Crawler $adCrawler): ?\DateTime;
}
