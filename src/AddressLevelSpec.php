<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure;

class AddressLevelSpec
{
    public const NAME_POSITION_BEFORE = 10;
    public const NAME_POSITION_AFTER = 20;

    private int $level;
    private string $name;
    private string $shortName;
    private int $namePosition;

    public function __construct(int $addressLevel, string $name, string $shortName, int $namePosition)
    {
        $this->level = $addressLevel;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->namePosition = $namePosition;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getNamePosition(): int
    {
        return $this->namePosition;
    }

    public function getLevel(): int
    {
        return $this->level;
    }
}
