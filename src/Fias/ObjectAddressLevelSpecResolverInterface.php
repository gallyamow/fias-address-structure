<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\AddressLevelSpec;

/**
 * Resolves AddressLevelSpec for levels by short name and fias level.
 */
interface ObjectAddressLevelSpecResolverInterface
{
    public function resolve(int $fiasLevel, $shortName): AddressLevelSpec;
}
