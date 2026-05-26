<?php

declare(strict_types=1);

namespace Commerce365\Core\Service;

use Commerce365\Core\Service\Request\Get;

class GetSalesDocuments
{
    public function __construct(
        private readonly Get $get,
        private readonly PrepareSalesRequestQuery $prepareSalesRequestQuery
    ) {}

    public function execute(array $query): array
    {
        $query = $this->prepareSalesRequestQuery->execute($query);

        return $this->get->execute('v2/SalesDocumentHistory/GetList', $query);
    }
}
