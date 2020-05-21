<?php

declare(strict_types=1);

namespace Notification;

use Formatter\SMSFormatter;
use Model\Ad;
use Twilio\Rest\Client;

class SMSNotifier implements NotifierInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function notify(Ad $ad): void
    {
        dump(sprintf('sending sms: %s', SMSFormatter::format($ad)));

        $message = $this->client->messages
            ->create($_ENV['TWILIO_RECIPIENT_PHONE_NUMBER'],
                [
                    'body' => SMSFormatter::format($ad),
                    'from' => $_ENV['TWILIO_SENDER_PHONE_NUMBER'],
                ]
            );
    }
}
