<?php

declare(strict_types=1);

namespace Database;

use Model\Ad;

class AdDatabase implements DatabaseInterface
{
    private $databaseFile;

    public function __construct(string $databaseFile)
    {
        $this->databaseFile = $databaseFile;
    }

    public function exists(Ad $ad): bool
    {
        return is_int(array_search($ad->id, array_column($this->readDatabase(), 'id')));
    }

    public function insert(Ad $ad): void
    {
        if ($this->exists($ad)) {
            return;
        }

        $this->writeDatabase(array_merge($this->readDatabase(), [json_decode(json_encode($ad), true)]));
    }

    public function getAds(): array
    {
        return $this->readDatabase();
    }

    private function readDatabase(): array
    {
        $data = file_get_contents($this->databaseFile);

        if (empty($data)) {
            return [];
        }

        return json_decode($data, true);
    }

    private function writeDatabase(array $data): void
    {
        file_put_contents($this->databaseFile, json_encode($data));
    }
}
