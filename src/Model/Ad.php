<?php

declare(strict_types=1);

namespace Model;

class Ad
{
    public $origin;
    public $id;
    public $url;

    public static function create(string $origin, string $id, string $url): self
    {
        $ad = new self();
        $ad->origin = $origin;
        $ad->id = $id;
        $ad->url = $url;

        return $ad;
    }
}
