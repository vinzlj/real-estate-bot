<?php

declare(strict_types=1);

namespace Formatter;

use DateTime;
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
                'price' => $ad->price,
                'city' => $ad->city,
                'title' => $ad->title,
                'publish_date' => $ad->publicationDate instanceof DateTime ? $ad->publicationDate->format('Y-m-d') : null,
                'url' => $ad->url,
            ];
        }, $ads);

        usort($ads, function (array $a, array $b): bool {
            if (!$a instanceof DateTime) {
                return false;
            }

            return $a['publish_date'] < $b['publish_date'];
        });

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
