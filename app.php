<?php

use Command\RunCrawlerCommand;
use Crawler\Century21Crawler;
use Crawler\CrawlerContainer;
use Crawler\OuestFranceCrawler;
use Crawler\SeLogerCrawler;
use Database\AdDatabase;
use Notification\NotificationManager;
use Notification\SMSNotifier;
use Symfony\Component\Console\Application;
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
    'https://www.ouestfrance-immo.com/louer/maison/nantes-44-44000/?prix=0_1000',
    'https://www.ouestfrance-immo.com/louer/maison/reze-44-44400/?prix=0_1000',
    'https://www.ouestfrance-immo.com/louer/maison/saint-sebastien-sur-loire-44-44230/?prix=0_1000'
]);

$century21Crawler = new Century21Crawler($httpClient, $database, $notificationManager, [
    'https://www.century21byouestsaintseb.com/annonces/location-maison/s-0-/st-0-/b-0-1000/',
]);

$seLogerCrawler = new SeLogerCrawler($httpClient, $database, $notificationManager, [
    'https://www.seloger.com/list.htm?ci=440109,440143,440190&idtt=1&idtypebien=2&pxmax=1000&tri=d_dt_crea',
]);

$crawlerContainer = new CrawlerContainer();
$crawlerContainer->addCrawler($ouestFranceCrawler);
$crawlerContainer->addCrawler($century21Crawler);
$crawlerContainer->addCrawler($seLogerCrawler);

$application = new Application();
$application->add(new RunCrawlerCommand($crawlerContainer, $notificationManager, $database));
$application->run();
