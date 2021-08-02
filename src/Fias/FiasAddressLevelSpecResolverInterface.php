<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\AddressLevelSpec;

/**
 * Resolves by single column like 'type_id'.
 */
interface FiasAddressLevelSpecResolverInterface
{
    public function resolve(int $typeId): AddressLevelSpec;
}
