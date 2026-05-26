<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Request\BusinessCentral;

use Commerce365\Core\Model\Command\GetOAuthToken;
use Commerce365\Core\Service\Logger;
use Commerce365\Core\Service\Request\PostInterface;
use Commerce365\Core\Service\Response\BusinessCentral\ProcessResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class OAuthPost implements PostInterface
{
    public function __construct(
        private readonly ProcessResponse $processResponse,
        private readonly RefreshOAuthToken $refreshOAuthToken,
        private readonly GetOAuthToken $getOAuthToken,
        private readonly GetBCEndpointUrl $getBCEndpointUrl,
        private readonly Logger $logger
    ) {}

    public function execute(string $method, array $postData = []): array
    {
        $endpointUrl = $this->getBCEndpointUrl->execute($method);
        if (!$endpointUrl) {
            return [];
        }

        $postData['json'] = $this->processJsonParams($postData['json']);

        try {
            $token = $this->getOAuthToken->execute();
            if (!$token) {
                $token = $this->refreshOAuthToken->execute();
            }

            if (!$token) {
                return [];
            }

            $response = $this->makeCall($endpointUrl, $token, $postData);
        } catch (GuzzleException $exception) {
            $this->logger->error(
                sprintf('BC OAuth request failed for %s: HTTP %d', $method, $this->getStatusCode($exception))
            );
            return [];
        }

        return $this->processResponse->execute($response);
    }

    private function makeCall(string $endpointUrl, string $token, array $postData, int $take = 1): ResponseInterface|array
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
            $this->logger->error(
                sprintf('BC OAuth request failed: HTTP %d', $this->getStatusCode($exception))
            );
            if ($take !== 2 && $exception->getCode() === 401) {
                $token = $this->refreshOAuthToken->execute();
                return $this->makeCall($endpointUrl, $token, $postData, 2);
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

    private function getStatusCode(GuzzleException $exception): int
    {
        return $exception instanceof ClientException && $exception->hasResponse()
            ? $exception->getResponse()->getStatusCode()
            : (int) $exception->getCode();
    }
}
