<?php

declare(strict_types=1);

namespace Commerce365\Core\Model\Command;

use Magento\Framework\App\ResourceConnection;

class SaveOAuthToken
{
    public function __construct(private readonly ResourceConnection $resourceConnection) {}

    public function execute($token, string $configHash): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('commerce365_oauth_token');
        $connection->insertOnDuplicate(
            $tableName,
            ['config_hash' => $configHash, 'token' => $token],
            ['token']
        );
    }
}
