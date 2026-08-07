<?php

declare(strict_types=1);

namespace Commerce365\Core\Service;

use Commerce365\Core\Service\Request\Get;

class GetSalesDocument
{
    public function __construct(
        private readonly Get $get,
        private readonly PrepareSalesRequestQuery $prepareSalesRequestQuery,
        private readonly CurrentStore $currentStore
    ) {}

    public function execute(array $query)
    {
        $storeId = $this->currentStore->getId();
        $query = $this->prepareSalesRequestQuery->execute($query, $storeId);

        return $this->get->execute('v2/SalesDocumentHistory/Get', $query, $storeId);
    }
}
