<?php

declare(strict_types=1);

namespace Formatter;

use Model\Ad;

class SMSFormatter
{
    public static function format(Ad $ad): string
    {
        return sprintf('New ad published on site %s: %s', $ad->origin, $ad->url);
    }
}
