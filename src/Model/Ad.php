<?php

declare(strict_types=1);

namespace Model;

use DateTime;

class Ad
{
    public $id;
    public $url;
    public $mainPicture;
    public $price;
    public $title;
    public $city;
    public $address;
    public $description;
    public $criterias;
    public $publicationDate;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setMainPicture(string $mainPicture): void
    {
        $this->mainPicture = $mainPicture;
    }

    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setCriterias(?string $criterias): void
    {
        $this->criterias = $criterias;
    }

    public function setPublicationDate(?DateTime $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }
}
