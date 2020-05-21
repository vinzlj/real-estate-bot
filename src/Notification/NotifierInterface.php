<?php

declare(strict_types=1);

namespace Notification;

use Model\Ad;

interface NotifierInterface
{
    public function notify(Ad $ad): void;
}
