<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository\Fias\LevelNameResolvers;

use CoreExtensions\AddressRepository\Exceptions\LevelNameNotFoundException;
use CoreExtensions\AddressRepository\LevelName;

class FiasObjectLevelNameResolver
{
    private FiasLevelNameSource $source;

    private static ?array $cachedItems = null;

    public function __construct(FiasLevelNameSource $typeSource)
    {
        $this->source = $typeSource;
    }

    public function resolve(int $fiasLevel, string $shortName): LevelName
    {
        if (null === static::$cachedItems) {
            $this->buildCache();
        }

        $key = $this->makeKey($fiasLevel, $shortName);

        if (!isset(self::$cachedItems[$key])) {
            throw LevelNameNotFoundException::withFiasRelationTypeAndTypeId('addr_obj_types', $key);
        }

        return self::$cachedItems[$key];
    }

    private function buildCache(): void
    {
        self::$cachedItems = [];

        $rows = $this->source->getItems();

        foreach ($rows as $row) {
            $fiasLevel = (int)$row['level'];
            $shortName = trim($row['shortname']);

            self::$cachedItems[$this->makeKey($fiasLevel, $shortName)] = new LevelName(
                $this->prepareString($row['name']),
                $this->prepareString($row['shortname'])
            );
        }
    }

    private function makeKey(int $fiasLevel, string $shortName): string
    {
        return $fiasLevel . '-' . $this->prepareString($shortName);
    }

    private function prepareString(string $shortName): string
    {
        // приводим все к нижнему регистру, даже аббревиатуры
        return mb_strtolower(trim($shortName));
    }
}
