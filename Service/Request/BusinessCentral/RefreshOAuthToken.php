<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Request\BusinessCentral;

use Commerce365\Core\Model\AdvancedConfig;
use Commerce365\Core\Model\Command\SaveOAuthToken;
use Commerce365\Core\Service\Response\ProcessResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\RuntimeException;

class RefreshOAuthToken
{
    public function __construct(
        private readonly AdvancedConfig $advancedConfig,
        private readonly ProcessResponse $processResponse,
        private readonly SaveOAuthToken $saveOAuthToken
    ) {}

    /**
     * @throws GuzzleException
     * @throws RuntimeException
     */
    public function execute(): string
    {
        $response = $this->sendRequest();
        if ($response->getStatusCode() !== 200) {
            $errorMessage = 'Error code: ' . $response->getStatusCode() . ' Reason: ' . $response->getReasonPhrase();
            throw new RuntimeException(__($errorMessage));
        }

        $tokenData = $this->processResponse->execute($response);
        if (!isset($tokenData['access_token'])) {
            throw new RuntimeException(__('Token is empty'));
        }

        $this->saveOAuthToken->execute($tokenData['access_token']);

        return $tokenData['access_token'];
    }

    /**
     * @throws GuzzleException
     * @throws RuntimeException
     */
    private function sendRequest(): \Psr\Http\Message\ResponseInterface
    {
        $tenantId = $this->advancedConfig->getTenantId();
        $clientId = $this->advancedConfig->getClientId();
        $clientSecret = $this->advancedConfig->getClientSecret();
        if (!$tenantId || !$clientId || !$clientSecret) {
            throw new RuntimeException(__('Please make sure to fill up all BC credentials in configuration'));
        }

        $endpointUrl = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/token';
        $postData['form_params'] = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'https://api.businesscentral.dynamics.com/.default'
        ];

        $client = new Client([
            'allow_redirects' => true,
            'http_errors' => false,
            'headers' => [
                'Accept' => '*/*',
                'Content-Type' => 'multipart/form-data'
            ]
        ]);

        return $client->post($endpointUrl, $postData);
    }
}
