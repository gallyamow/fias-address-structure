<?php

declare(strict_types=1);

namespace Addresser\FiasAddressStructure\Fias;

use Addresser\FiasAddressStructure\AddressLevelSpec;

/**
 * Resolves AddressLevelSpec by exact type id.
 */
interface TypeAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec;
}
