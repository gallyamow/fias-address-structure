<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\AddressLevelSpec;

/**
 * Resolves AddressLevelSpec by exact type id.
 */
interface TypeAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec;
}
