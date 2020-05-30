<?php

declare(strict_types=1);

namespace Database;

use Model\Ad;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AdDatabase implements DatabaseInterface
{
    private $databaseFile;
    private $newAds = [];
    private $objectNormalizer;

    public function __construct(ObjectNormalizer $objectNormalizer, string $databaseFile)
    {
        $this->objectNormalizer = $objectNormalizer;
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

        $this->newAds[] = $ad;

        $this->writeDatabase(array_merge($this->readDatabase(), [json_decode(json_encode($ad), true)]));
    }

    public function getAds(): array
    {
        $ads = [];

        foreach ($this->readDatabase() as $arrayAd) {
            $ads[] = $this->objectNormalizer->denormalize($arrayAd, Ad::class);
        }

        return $ads;
    }

    public function getNewAds(): array
    {
        return $this->newAds;
    }

    private function readDatabase(): array
    {
        if (!file_exists($this->databaseFile)) {
            file_put_contents($this->databaseFile, '');
        }

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
