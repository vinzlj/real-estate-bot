<?php

declare(strict_types=1);

namespace Notification;

use Model\Ad;

class NotificationManager
{
    /** @var NotifierInterface[] */
    private $notifiers = [];

    public function addNotifier(NotifierInterface $notifier): void
    {
        $this->notifiers[] = $notifier;
    }

    public function notify(Ad $ad): void
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->notify($ad);
        }
    }
}
