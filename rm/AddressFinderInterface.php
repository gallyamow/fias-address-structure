<?php

declare(strict_types=1);

namespace CoreExtensions\AddressRepository;

interface AddressFinderInterface
{
    /**
     * @param array $options
     * @return \Iterator<Address>
     */
    public function find(array $options): \Iterator;
}
