<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Config;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Resolves the admin System Configuration scope (store view / website / default)
 * currently being edited to a store id, so configuration reads fall back
 * store view -> website -> default. Returns null for the default scope.
 */
class ScopeResolver
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly StoreManagerInterface $storeManager
    ) {}

    public function getStoreId(): ?int
    {
        $store = $this->request->getParam('store');
        if ($store !== null && $store !== '') {
            return (int) $store;
        }

        $website = $this->request->getParam('website');
        if ($website !== null && $website !== '') {
            try {
                $defaultStore = $this->storeManager->getWebsite($website)->getDefaultStore();
                return $defaultStore ? (int) $defaultStore->getId() : null;
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
