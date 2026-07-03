<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Request\BusinessCentral;

use Commerce365\Core\Model\AdvancedConfig;
use Commerce365\Core\Service\Logger;
use Commerce365\Core\Service\Request\PostInterface;
use Commerce365\Core\Service\Response\BusinessCentral\ProcessResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class BasicPost implements PostInterface
{
    public function __construct(
        private readonly AdvancedConfig $advancedConfig,
        private readonly ProcessResponse $processResponse,
        private readonly GetBCEndpointUrl $getBCEndpointUrl,
        private readonly Logger $logger
    ) {}

    public function execute($method, $postData = [], ?int $storeId = null): array
    {
        $endpointUrl = $this->getBCEndpointUrl->execute($method, $storeId);
        if (!$endpointUrl) {
            return [];
        }

        $username = $this->advancedConfig->getUsername($storeId);
        $password = $this->advancedConfig->getPassword($storeId);

        $postData['json'] = $this->processJsonParams($postData['json']);
        $postData['auth'] = [$username, $password];

        try {
            $response = $this->makeCall($endpointUrl, $postData);
        } catch (GuzzleException|ClientException $exception) {
            $this->logger->error($exception->getMessage());
            return [];
        }

        return $this->processResponse->execute($response);
    }

    /**
     * @throws GuzzleException
     */
    private function makeCall($endpointUrl, $postData)
    {
        $client = new Client([
            'headers' => [
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]
        ]);

        try {
            return $client->post($endpointUrl, $postData);
        } catch (ClientException $exception) {
            $this->logger->error($exception->getMessage());
            return [];
        }
    }

    private function processJsonParams(array $jsonData): array
    {
        foreach ($jsonData as $key => $param) {
            if (is_array($param)) {
                $jsonData[$key] = '[' . implode(',', $param) . ']';
            }
        }

        return $jsonData;
    }
}
