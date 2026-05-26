<?php

declare(strict_types=1);

namespace Commerce365\Core\Service;

use Commerce365\Core\Model\MainConfig;
use Commerce365\Core\Service\Customer\ParentResolveCustomerSession;

class PrepareSalesRequestQuery
{
    public function __construct(
        private readonly MainConfig $mainConfig,
        private readonly ParentResolveCustomerSession $customerSession
    ) {}

    public function execute(array $query): array
    {
        $query['customerId'] = $this->customerSession->getCustomer()->getId();

        //config returns 0 or 1, but we need false or true for our querystring parameter
        $query['webOrdersOnly'] = true;
        if ($this->mainConfig->getIncludeNonWebOrders()) {
            $query['webOrdersOnly'] = false;
        }

        $query['releasedOnly'] = true;

        return $query;
    }
}
