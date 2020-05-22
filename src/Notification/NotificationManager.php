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

    /**
     * @param Ad[] $ads
     */
    public function notify(array $ads): void
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->notify($ads);
        }
    }
}
