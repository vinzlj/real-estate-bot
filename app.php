<?php

use Crawler\OuestFranceCrawler;
use Symfony\Component\HttpClient\HttpClient;

require_once 'vendor/autoload.php';

$ouestFranceCrawler = new OuestFranceCrawler(HttpClient::create(), [
    'Nantes' => 'https://www.ouestfrance-immo.com/louer/maison/nantes-44-44000/?prix=0_1000',
    'Rezé' => 'https://www.ouestfrance-immo.com/louer/maison/reze-44-44400/?prix=0_1000',
    'Saint-Sebastien' => 'https://www.ouestfrance-immo.com/louer/maison/saint-sebastien-sur-loire-44-44230/?prix=0_1000'
]);

$crawlers = [
    $ouestFranceCrawler,
];

foreach ($crawlers as $crawler) {
    $crawler->crawl();
    $crawler->display();
}
