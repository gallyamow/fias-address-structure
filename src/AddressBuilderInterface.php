<?php

namespace CoreExtensions\AddressRepository;

interface AddressBuilderInterface
{
    /**
     * Builds new entity based on passed data or applies changes to exists entity (in order to make partial update).
     *
     * @param array $data
     * @param Address|null $existsAddress
     * @return Address
     */
    function build(array $data, ?Address $existsAddress = null): Address;
}