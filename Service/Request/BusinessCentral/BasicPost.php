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
use Psr\Http\Message\ResponseInterface;

class BasicPost implements PostInterface
{
    public function __construct(
        private readonly AdvancedConfig $advancedConfig,
        private readonly ProcessResponse $processResponse,
        private readonly GetBCEndpointUrl $getBCEndpointUrl,
        private readonly Logger $logger
    ) {}

    public function execute(string $method, array $postData = []): array
    {
        $endpointUrl = $this->getBCEndpointUrl->execute($method);
        if (!$endpointUrl) {
            return [];
        }

        $username = $this->advancedConfig->getUsername();
        $password = $this->advancedConfig->getPassword();

        $postData['json'] = $this->processJsonParams($postData['json']);
        $postData['auth'] = [$username, $password];

        try {
            $response = $this->makeCall($endpointUrl, $postData);
        } catch (GuzzleException $exception) {
            $this->logger->error(
                sprintf('BC API request failed for %s: HTTP %d', $method, $this->getStatusCode($exception))
            );
            return [];
        }

        return $this->processResponse->execute($response);
    }

    /**
     * @throws GuzzleException
     */
    private function makeCall(string $endpointUrl, array $postData): ResponseInterface|array
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
            $this->logger->error(
                sprintf('BC API request failed: HTTP %d', $this->getStatusCode($exception))
            );
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
