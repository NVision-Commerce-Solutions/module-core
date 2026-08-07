<?php

namespace Commerce365\Core\Controller\Adminhtml\Connection;

use Commerce365\Core\Model\AdvancedConfig;
use Commerce365\Core\Service\Config\ScopeResolver;
use Commerce365\Core\Service\Request\BusinessCentral\RefreshOAuthToken;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;

class OAuthCheck extends Action
{
    private AdvancedConfig $advancedConfig;
    private RefreshOAuthToken $refreshOAuthToken;
    private ScopeResolver $scopeResolver;

    public function __construct(
        Context $context,
        RefreshOAuthToken $refreshOAuthToken,
        AdvancedConfig $advancedConfig,
        ManagerInterface $messageManager,
        ScopeResolver $scopeResolver
    ) {
        parent::__construct($context);
        $this->messageManager = $messageManager;
        $this->advancedConfig = $advancedConfig;
        $this->refreshOAuthToken = $refreshOAuthToken;
        $this->scopeResolver = $scopeResolver;
    }

    public function execute()
    {
        $storeId = $this->scopeResolver->getStoreId();
        $endpoint = $this->advancedConfig->getEndpoint($storeId);
        $tenantId = $this->advancedConfig->getTenantId($storeId);
        $clientId = $this->advancedConfig->getClientId($storeId);
        $clientSecret = $this->advancedConfig->getClientSecret($storeId);

        if (!$endpoint || !$tenantId || !$clientId || !$clientSecret) {
            $this->messageManager->addErrorMessage(__('First fill in all of the above fields!'));

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        try {
            $this->refreshOAuthToken->execute($storeId);
            $this->messageManager->addSuccessMessage(__('Connected Successful'));

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }
    }

    protected function _isAllowed(): bool
    {
        return true;
    }
}
