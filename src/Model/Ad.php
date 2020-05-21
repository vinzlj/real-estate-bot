<?php

declare(strict_types=1);

namespace Model;

use DateTime;

class Ad
{
    public $origin;
    public $id;
    public $url;
    public $mainPicture;
    public $price;
    public $city;
    public $address;
    public $title;
    public $description;
    public $criterias;
    public $publicationDate;

    public static function create(
        string $origin,
        int $id,
        string $url,
        string $mainPicture,
        ?int $price,
        ?string $city,
        ?string $address,
        ?string $title,
        ?string $description,
        ?string $criterias,
        ?DateTime $publicationDate
    ): self {
        $ad = new self();
        $ad->origin = $origin;
        $ad->id = $id;
        $ad->url = $url;
        $ad->mainPicture = $mainPicture;
        $ad->price = $price;
        $ad->city = $city;
        $ad->address = $address;
        $ad->title = $title;
        $ad->description = $description;
        $ad->criterias = $criterias;
        $ad->publicationDate = $publicationDate;

        return $ad;
    }
}
