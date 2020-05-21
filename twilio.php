<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Twilio\Rest\Client;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$twilio = new Client($_ENV['TWILIO_SID'], $_ENV['TWILIO_TOKEN']);

$message = $twilio->messages
    ->create($_ENV['TWILIO_RECIPIENT_PHONE_NUMBER'],
        [
            'body' => 'yo sup',
            'from' => $_ENV['TWILIO_SENDER_PHONE_NUMBER'],
        ]
    );

dump($message->sid);
