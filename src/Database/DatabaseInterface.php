<?php

namespace Database;

use Model\Ad;

interface DatabaseInterface
{
    public function exists(Ad $ad): bool;
    public function insert(Ad $ad): void;
    public function getNewAds(): array;
}
