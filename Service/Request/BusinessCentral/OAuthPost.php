<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Request\BusinessCentral;

use Commerce365\Core\Model\AdvancedConfig;
use Commerce365\Core\Model\Command\GetOAuthToken;
use Commerce365\Core\Service\Logger;
use Commerce365\Core\Service\Request\PostInterface;
use Commerce365\Core\Service\Response\BusinessCentral\ProcessResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class OAuthPost implements PostInterface
{
    public function __construct(
        private readonly ProcessResponse $processResponse,
        private readonly RefreshOAuthToken $refreshOAuthToken,
        private readonly GetOAuthToken $getOAuthToken,
        private readonly GetBCEndpointUrl $getBCEndpointUrl,
        private readonly AdvancedConfig $advancedConfig,
        private readonly Logger $logger
    ) {}

    public function execute($method, $postData = [], ?int $storeId = null): array
    {
        $endpointUrl = $this->getBCEndpointUrl->execute($method, $storeId);
        if (!$endpointUrl) {
            return [];
        }

        $postData['json'] = $this->processJsonParams($postData['json']);

        try {
            $configHash = $this->advancedConfig->getInstanceHash($storeId);
            $token = $this->getOAuthToken->execute($configHash);
            if (!$token) {
                $token = $this->refreshOAuthToken->execute($storeId);
            }

            if (!$token) {
                return [];
            }

            $response = $this->makeCall($endpointUrl, $token, $postData, $storeId);
        } catch (GuzzleException $exception) {
            $this->logger->error($exception->getMessage());
            return [];
        }

        return $this->processResponse->execute($response);
    }

    private function makeCall($endpointUrl, $token, $postData, ?int $storeId = null, $take = 1)
    {
        $client = new Client([
            'headers' => [
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        try {
            return $client->post($endpointUrl, $postData);
        } catch (ClientException $exception) {
            $this->logger->error($exception->getMessage());
            if ($take !== 2 && $exception->getCode() === 401) {
                $token = $this->refreshOAuthToken->execute($storeId);
                return $this->makeCall($endpointUrl, $token, $postData, $storeId, 2);
            }

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
