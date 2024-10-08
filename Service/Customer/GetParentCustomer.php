<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GetParentCustomer
{
    public function __construct(private readonly CustomerRepositoryInterface $customerRepository) {}

    public function execute(CustomerInterface $customer): CustomerInterface
    {
        if (!$customer->getCustomAttribute('parent_customer_id')) {
            return $customer;
        }

        $parentId = $customer->getCustomAttribute('parent_customer_id')->getValue();
        try {
            return $this->customerRepository->getById($parentId);
        } catch (NoSuchEntityException $e) {
            return $customer;
        }
    }

    public function getByCustomerId($customerId): ?CustomerInterface
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return null;
        }

        return $this->execute($customer);
    }
}
