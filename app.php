<?php

use Crawler\OuestFranceCrawler;
use Database\AdDatabase;
use Symfony\Component\HttpClient\HttpClient;

require_once 'vendor/autoload.php';

$httpClient = HttpClient::create();
$database = new AdDatabase(__DIR__.'/data/database.json');

$ouestFranceCrawler = new OuestFranceCrawler($httpClient, $database, [
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
