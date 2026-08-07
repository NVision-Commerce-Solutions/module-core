<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Request;

use Commerce365\Core\Service\Response\ProcessResponse;
use GuzzleHttp\Exception\GuzzleException;

class Get
{
    public function __construct(
        private readonly GetClient $getClient,
        private readonly ProcessResponse $processResponse
    ) {}

    /**
     * @throws GuzzleException
     */
    public function execute($endpoint, $query, ?int $storeId = null): array
    {
        $client = $this->getClient->execute($storeId);

        if (!$client) {
            return [];
        }

        $response = $client->get($endpoint, ['query' => $query]);

        return $this->processResponse->execute($response);
    }
}
