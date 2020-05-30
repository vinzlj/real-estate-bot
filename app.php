<?php

use Command\RunCrawlerCommand;
use Configuration\AppConfiguration;
use Crawler\CrawlerContainer;
use Database\AdDatabase;
use Notification\NotificationManager;
use Notification\SMSNotifier;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Yaml\Yaml;
use Twilio\Rest\Client;

require_once 'vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// configuration
$configuration = (new Processor())->processConfiguration(new AppConfiguration(), Yaml::parse(file_get_contents(__DIR__.'/config/config.yaml')));

// database
$database = new AdDatabase(new ObjectNormalizer(), sprintf('%s/%s', __DIR__, $configuration['database_path']));

// crawlers
$crawlerContainer = new CrawlerContainer($configuration['crawlers'], HttpClient::create(), $database);

// notification
$smsNotifier = new SMSNotifier(new Client($_SERVER['TWILIO_SID'], $_SERVER['TWILIO_TOKEN']));
$notificationManager = new NotificationManager();
$notificationManager->addNotifier($smsNotifier);

// application
$application = new Application();
$application->add(new RunCrawlerCommand($crawlerContainer, $notificationManager, $database));
$application->run();
