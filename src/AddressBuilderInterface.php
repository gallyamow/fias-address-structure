<?php

namespace Addresser\FiasAddressStructure;

interface AddressBuilderInterface
{
    /**
     * Builds new entity based on passed data or applies changes to exists entity (in order to make partial update).
     */
    public function build(array $indexerQueueRow, ?Address $existsAddress = null): Address;
}
