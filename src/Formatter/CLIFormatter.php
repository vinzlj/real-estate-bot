<?php

declare(strict_types=1);

namespace Formatter;

use Model\Ad;

class CLIFormatter
{
    /**
     * @param Ad[] $ads
     */
    public static function format(array $ads): array
    {
        $ads = array_map(function (Ad $ad) {
            return [
                'origin' => $ad->origin,
                'url' => $ad->url,
            ];
        }, $ads);

        return $ads;
    }

    /**
     * @param Ad[] $ads
     */
    public static function getHeaders(array $ads): array
    {
        return array_keys(static::format($ads)[0]);
    }
}
