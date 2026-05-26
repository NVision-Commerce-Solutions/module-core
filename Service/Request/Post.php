<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Request;

use Commerce365\Core\Service\Response\ProcessResponse;
use GuzzleHttp\Exception\GuzzleException;

class Post implements PostInterface
{
    public function __construct(
        private readonly GetClient $getClient,
        private readonly ProcessResponse $processResponse
    ) {}

    /**
     * @throws GuzzleException
     */
    public function execute(string $method, array $postData = []): array
    {
        $client = $this->getClient->execute();

        if (!$client) {
            return [];
        }

        $response = $client->post($method, $postData);

        return $this->processResponse->execute($response);
    }
}
