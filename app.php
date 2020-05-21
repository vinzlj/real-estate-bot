<?php

use Crawler\OuestFranceCrawler;
use Database\AdDatabase;
use Notification\NotificationManager;
use Notification\SMSNotifier;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;
use Twilio\Rest\Client;

require_once 'vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$httpClient = HttpClient::create();
$database = new AdDatabase(__DIR__.'/data/database.json');

$smsNotifier = new SMSNotifier(new Client($_ENV['TWILIO_SID'], $_ENV['TWILIO_TOKEN']));
$notificationManager = new NotificationManager();
$notificationManager->addNotifier($smsNotifier);

$ouestFranceCrawler = new OuestFranceCrawler($httpClient, $database, $notificationManager, [
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
