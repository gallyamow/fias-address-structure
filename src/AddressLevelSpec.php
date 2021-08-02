<?php

declare(strict_types=1);

namespace Addresser\AddressRepository;

class AddressLevelSpec
{
    public const NAME_POSITION_BEFORE = 10;
    public const NAME_POSITION_AFTER = 20;

    private int $level;
    private string $name;
    private string $shortName;
    private int $namePosition;

    /**
     * @param int $addressLevel
     * @param string $name
     * @param string $shortName
     * @param int $namePosition
     */
    public function __construct(int $addressLevel, string $name, string $shortName, int $namePosition)
    {
        $this->level = $addressLevel;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->namePosition = $namePosition;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return int
     */
    public function getNamePosition(): int
    {
        return $this->namePosition;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}


