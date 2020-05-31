<?php

declare(strict_types=1);

namespace Crawler\Implementation;

use Crawler\AdCrawlerInterface;
use Crawler\BaseCrawler;
use Event\CrawlingUrlEvent;
use Model\Ad;
use Symfony\Component\DomCrawler\Crawler;

class SquareHabitatCrawler extends BaseCrawler implements AdCrawlerInterface
{
    public function crawl(): void
    {
        foreach ($this->urls as $url) {
            $this->eventDispatcher->dispatch(new CrawlingUrlEvent($url), CrawlingUrlEvent::NAME);

            $this->crawlUrlWithPost($url);
        }
    }

    public function crawlUrlWithPost(string $url): void
    {
        $response = $this->client->request('POST', $url, [
            'verify_peer' => false,
            'headers' => $this->getRequestHeaders(),
            'body' => [
                '__EVENTTARGET' => 'ctl00$cphMid$lnkRecherche',
                '__VIEWSTATE' => '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',
                '__VIEWSTATEGENERATOR' => '6A4191C2',
                '__EVENTVALIDATION' => 'vhnooj4WTUD12r7Jkopl6HytDn9oz3hda5XioKMvzUwRrxiC5TL9psacRuyffegaWjdvR2156lFh6NUr1T0PpUIMohwDKMPzHCyejX8borhEEyhfowZ1w9FCEeaBsU2P167Fk7aym5ftpNGbi4+Q+RzhUN6xkSyPeRaLMl8ePVzEX+4lYvFd90bV4g57hTe3Tx3LmVQgD8G89LlCkfesRc9TyaEScQ1MOCGbBTZ/UTTTzF8b8pX47GSIfdfOEyaAiE0M2QcYa/ho0I9I9EIDTXvr9f+YlNiA95FWLer22MQ4m0Nq5ptFQMPE1NXwV4Rrc0XlTcSKTsONHSOaFKeWOBt76BYeLm8t5pmyBst7AFadDolXaFFPSwOlYPyrNCcXdzpK0Bs+Dv/6Io/Rmx1lvrq7AUWP8wQd70XzKJ3u+7aqvxc9EjHox/swXJLjTIQEssDfrY7Fi5100tZigIPopokDc/OSSRVq7TIsKg7Ocqlfm4N5L9rJQwMnwo4S1Gq/JGq3+A4oRJXDGpHIxa8ANRzBQbEX6BQnINkhcrQ/FdWxz1M6Beuf7R4GdVUsFpXqek6ORIWJkGpFDxJYdB6APHJ5ZY05qQ0y+T7sXfCV0YwmTmxp',
                'ctl00$cphMid$search' => 'Ville,+département,+code+postal',
                'ctl00$cphMid$search_id' => '44190_3',
                'ctl00$cphMid$lstTypeBien' => 15,
                'ctl00$cphMid$txtPrixMin' => 0,
                'ctl00$cphMid$txtPrixMax' => 'Max',
                'ctl00$cphMid$txtSurfaceMin' => 0,
                'ctl00$cphMid$txtSurfaceMax' => 'Max',
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
        return $adCrawler->filter('a')->attr('id');
    }

    public function extractAdUrl(Crawler $adCrawler): string
    {
        return sprintf('%s%s', $this->baseUrl, $adCrawler->filter('a')->attr('href'));
    }

    protected function getRequestHeaders(): array
    {
        return [
            'authority' => ' www.squarehabitat.fr',
            'pragma' => ' no-cache',
            'cache-control' => 'no-cache',
            'upgrade-insecure-requests' => '1',
            'origin' => ' https://www.squarehabitat.fr',
            'content-type' => ' application/x-www-form-urlencoded',
            'user-agent' => ' Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.61 Safari/537.36',
            'accept' => ' text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'sec-fetch-site' => ' same-origin',
            'sec-fetch-mode' => ' navigate',
            'sec-fetch-user' => ' ?1',
            'sec-fetch-dest' => ' document',
            'referer' => ' https://www.squarehabitat.fr/louer-maison-saint_sebastien_sur_loire-44230.aspx',
            'accept-language' => ' fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
            'cookie' => 'SquareHabitat=utpyrtvrun1umk4rzikfcy3y; __utma=224339268.1941567657.1590939694.1590939694.1590939694.1; __utmc=224339268; __utmz=224339268.1590939694.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); __utmt=1; _fbp=fb.1.1590939694441.389840229; cb-enabled=accepted; cookie_pub=True; cookie_stat=True; __utmb=224339268.3.9.1590939712310; cb-enabled=accepted; cookie_pub=True; cookie_stat=True'
        ];
    }
}
