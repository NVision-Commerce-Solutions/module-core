<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Customer;

class GetParentCustomerId
{
    public function __construct(private readonly GetParentCustomer $getParentCustomer) {}

    public function execute(int|string $customerId): int|string
    {
        $parentCustomer = $this->getParentCustomer->getByCustomerId($customerId);

        return $parentCustomer ? $parentCustomer->getId() : $customerId;
    }
}
