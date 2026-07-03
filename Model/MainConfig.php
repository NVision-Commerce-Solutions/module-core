<?php

declare(strict_types=1);

namespace Commerce365\Core\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class MainConfig
{
    private const XML_PATH_NONWEB_ORDERS = 'b2b/b2b_offlineorders';
    private const XML_PATH_HUB_URL = 'hub/hub_url';
    private const XML_PATH_HUB_APPID = 'hub/hub_appid';
    private const XML_PATH_HUB_SECRETKEY = 'hub/hub_secretkey';
    private const XML_PATH_CONFIGURABLE_IMAGE_ENABLED = 'configurable_image/enabled';
    private const XML_PATH_CONFIGURABLE_IMAGE_REPLACE = 'configurable_image/replace_existing';

    public function __construct(private readonly ScopeConfigInterface $scopeConfig) {}

    public function getIncludeNonWebOrders($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NONWEB_ORDERS, $storeId);
    }

    public function getHubUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HUB_URL, $storeId);
    }

    public function getHubAppId($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HUB_APPID, $storeId);
    }

    public function getHubSecretKey($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HUB_SECRETKEY, $storeId);
    }

    public function isConfigurableImageEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CONFIGURABLE_IMAGE_ENABLED, $storeId);
    }

    public function isConfigurableImageReplaceExisting($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CONFIGURABLE_IMAGE_REPLACE, $storeId);
    }

    /**
     * Reads at store scope so values fall back store view -> website -> default.
     */
    public function getConfigValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'commerce365config_general/' . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
