<?php

declare(strict_types=1);

namespace Commerce365\Core\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Request-scoped holder for the store a Commerce365 operation runs against.
 *
 * Defaults to the active store, but can be overridden (e.g. from the REST API,
 * where there is no natural storefront context) via setId(). Shared across the
 * Core and dependent modules so every configuration / ERP read resolves to the
 * same scope within a request.
 */
class CurrentStore
{
    private int $currentStoreId = 0;

    public function __construct(private readonly StoreManagerInterface $storeManager) {}

    public function setId($storeId): void
    {
        $this->currentStoreId = (int) $storeId;
    }

    /**
     * @throws LocalizedException
     */
    public function getId(): int
    {
        if (!$this->exists()) {
            $this->currentStoreId = (int) $this->storeManager->getStore()->getId();
        }

        if (!$this->exists()) {
            throw new LocalizedException(__('Current store does not exist.'));
        }

        return $this->currentStoreId;
    }

    public function exists(): bool
    {
        return (bool) $this->currentStoreId;
    }
}
