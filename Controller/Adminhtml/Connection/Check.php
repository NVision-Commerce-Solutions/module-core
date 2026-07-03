<?php

namespace Commerce365\Core\Controller\Adminhtml\Connection;

use Commerce365\Core\Model\MainConfig;
use Commerce365\Core\Service\Config\ScopeResolver;
use Commerce365\Core\Service\Request\Post;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;

class Check extends Action
{
    private Post $post;
    private MainConfig $mainConfig;
    private ScopeResolver $scopeResolver;

    public function __construct(
        Post $post,
        Context $context,
        MainConfig $mainConfig,
        ManagerInterface $messageManager,
        ScopeResolver $scopeResolver
    ) {
        parent::__construct($context);
        $this->post = $post;
        $this->mainConfig = $mainConfig;
        $this->messageManager = $messageManager;
        $this->scopeResolver = $scopeResolver;
    }

    public function execute()
    {
        $storeId = $this->scopeResolver->getStoreId();
        $apiUrl = $this->mainConfig->getHubUrl($storeId);
        $appId = $this->mainConfig->getHubAppId($storeId);
        $secretKey = $this->mainConfig->getHubSecretKey($storeId);

        if (!$apiUrl || !$appId || !$secretKey) {
            $this->messageManager->addErrorMessage(__('First fill in all of the above fields!'));
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        try {
            $body = $this->post->execute(
                'ConnectionStatus',
                ['json' => [
                    'AppId' => $appId,
                    'SecretKey' => $secretKey
                ]],
                $storeId
            );

            $apiConnectionStatusCode = $body["bcApiStatusCode"] ?? 500;
            $statusMessage = $body["bcApiConnectionMessage"] ?? __('Unknown Error');

            if ($apiConnectionStatusCode === 200) {
                $this->messageManager->addSuccessMessage(__('Connected Successful'));
                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
            }

            $this->messageManager->addErrorMessage($statusMessage);
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
