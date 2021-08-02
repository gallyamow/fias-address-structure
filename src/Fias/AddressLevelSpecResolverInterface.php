<?php

declare(strict_types=1);

namespace Addresser\AddressRepository\Fias;

use Addresser\AddressRepository\AddressLevelSpec;

interface AddressLevelSpecResolverInterface
{
    /**
     * @param int $addressLevel
     * @param string|int $identifier
     * @return AddressLevelSpec
     */
    public function resolve(int $addressLevel, $identifier): AddressLevelSpec;
}
