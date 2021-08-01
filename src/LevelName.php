<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository;

/**
 * @see gar.*_types
 */
class LevelName
{
    private string $name;
    private string $shortName;

    /**
     * @param string $name
     * @param string $shortName
     */
    public function __construct(string $name, string $shortName)
    {
        $this->name = $name;
        $this->shortName = $shortName;
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
}


