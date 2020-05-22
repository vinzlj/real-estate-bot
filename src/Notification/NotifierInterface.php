<?php

declare(strict_types=1);

namespace Notification;

use Model\Ad;

interface NotifierInterface
{
    /**
     * @param Ad[] $ads
     */
    public function notify(array $ads): void;
}
