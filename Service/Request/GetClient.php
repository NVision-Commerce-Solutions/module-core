<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Request;

use Commerce365\Core\Model\MainConfig;
use GuzzleHttp\Client;

class GetClient
{
    public function __construct(private readonly MainConfig $mainConfig) {}

    public function execute(?int $storeId = null): ?Client
    {
        $hubUrl = $this->mainConfig->getHubUrl($storeId);
        $hubAppId = $this->mainConfig->getHubAppId($storeId);
        $hubSecretKey = $this->mainConfig->getHubSecretKey($storeId);
        if (!$hubAppId || !$hubUrl || !$hubSecretKey) {
            return null;
        }

        $hubUrl = rtrim($hubUrl, '/') . '/';

        return new Client([
            'base_uri' => $hubUrl . 'api/',
            'verify' => false,
            'allow_redirects' => true,
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'appId' => $hubAppId,
                'secretKey' => $hubSecretKey
            ]
        ]);
    }
}
