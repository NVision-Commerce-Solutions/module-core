<?php

declare(strict_types=1);

namespace Commerce365\Core\Model\Command;

use Magento\Framework\App\ResourceConnection;

class GetOAuthToken
{
    public function __construct(private readonly ResourceConnection $resourceConnection) {}

    public function execute(string $configHash): string
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('commerce365_oauth_token');
        $select = $connection->select()
            ->from($tableName, ['token'])
            ->where('config_hash = ?', $configHash)
            ->limit(1);

        return (string) $connection->fetchOne($select);
    }
}
