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

    /**
     * @param Ad[]
     */
    public function notify(array $ads): void
    {
        if (0 === count($ads)) {
            return;
        }

        foreach ($ads as $ad) {
            $this->sendMessage($ad);
        }
    }

    private function sendMessage(Ad $ad): void
    {
        dump(sprintf('sending sms: %s', SMSFormatter::format($ad)));

        $message = $this->client->messages
            ->create(
                $_SERVER['TWILIO_RECIPIENT_PHONE_NUMBER'],
                [
                    'body' => SMSFormatter::format($ad),
                    'from' => $_SERVER['TWILIO_SENDER_PHONE_NUMBER'],
                ]
            );
    }
}
