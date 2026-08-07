<?php

declare(strict_types=1);

namespace Commerce365\Core\Model;

use Commerce365\Core\Model\Config\Source\AuthType;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AdvancedConfig
{
    private const XML_PATH_TENANT_ID = 'bc_config/tenant_id';
    private const XML_PATH_ENVIRONMENT = 'bc_config/environment';
    private const XML_PATH_CLIENT_ID = 'bc_config/client_id';
    private const XML_PATH_AUTH_TYPE = 'bc_config/auth_type';
    private const XML_PATH_ENABLED = 'bc_config/enabled';
    private const XML_PATH_CLIENT_SECRET = 'bc_config/client_secret';
    private const XML_PATH_COMPANY_NAME = 'bc_config/company';
    private const XML_PATH_ENDPOINT = 'bc_config/endpoint';
    private const XML_PATH_USERNAME = 'bc_config/username';
    private const XML_PATH_PASSWORD = 'bc_config/password';

    public function __construct(private readonly ScopeConfigInterface $scopeConfig) {}

    public function isBCOAuth($storeId = null): bool
    {
        $isEnabled = $this->isSetConfigFlag(self::XML_PATH_ENABLED, $storeId);
        $type = $this->getConfigValue(self::XML_PATH_AUTH_TYPE, $storeId);

        return $type === AuthType::AUTH_TYPE_OAUTH && $isEnabled;
    }

    public function isBCBasic($storeId = null): bool
    {
        $isEnabled = $this->isSetConfigFlag(self::XML_PATH_ENABLED, $storeId);
        $type = $this->getConfigValue(self::XML_PATH_AUTH_TYPE, $storeId);

        return $type === AuthType::AUTH_TYPE_BASIC && $isEnabled;
    }

    public function getTenantId($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_TENANT_ID, $storeId);
    }

    public function getEnvironment($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ENVIRONMENT, $storeId);
    }

    public function getClientId($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CLIENT_ID, $storeId);
    }

    public function getClientSecret($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CLIENT_SECRET, $storeId);
    }

    public function getEndpoint($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ENDPOINT, $storeId);
    }

    public function getCompany($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_COMPANY_NAME, $storeId);
    }

    public function getUsername($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_USERNAME, $storeId);
    }

    public function getPassword($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_PASSWORD, $storeId);
    }

    /**
     * Stable identifier for the BC instance a given scope resolves to.
     *
     * Used to key cached artefacts (e.g. OAuth tokens) per instance so that
     * scopes pointing at different Business Central environments never share
     * credentials, while scopes resolving to the same instance reuse them.
     */
    public function getInstanceHash($storeId = null): string
    {
        return sha1(implode('|', [
            (string) $this->getTenantId($storeId),
            (string) $this->getClientId($storeId),
            (string) $this->getEnvironment($storeId),
            (string) $this->getEndpoint($storeId),
            (string) $this->getCompany($storeId),
        ]));
    }

    /**
     * Reads at store scope so values fall back store view -> website -> default.
     */
    public function getConfigValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'commerce365config_advanced/' . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isSetConfigFlag($path, $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'commerce365config_advanced/' . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
