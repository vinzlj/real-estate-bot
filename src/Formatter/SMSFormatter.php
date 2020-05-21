<?php

declare(strict_types=1);

namespace Formatter;

use Model\Ad;

class SMSFormatter
{
    public static function format(Ad $ad)
    {
        return sprintf('Une nouvelle annonce a été publiée sur le site %s: %s', $ad->origin, $ad->url);
    }
}
