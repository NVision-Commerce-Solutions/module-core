<?php

declare(strict_types=1);

namespace Commerce365\Core\Service\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\AccountConfirmation;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;
use Magento\Customer\Model\Session;
use Magento\Framework\Session\Generic;

class ParentResolveCustomerSession extends Session
{
    private GetParentCustomer $getParentCustomer;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        Share $configShare,
        \Magento\Framework\Url\Helper\Data $coreUrl,
        \Magento\Customer\Model\Url $customerUrl,
        ResourceCustomer $customerResource,
        CustomerFactory $customerFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        Generic $session,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Http\Context $httpContext,
        CustomerRepositoryInterface $customerRepository,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\App\Response\Http $response,
        GetParentCustomer $getParentCustomer,
        ?AccountConfirmation $accountConfirmation = null
    ) {
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $configShare,
            $coreUrl,
            $customerUrl,
            $customerResource,
            $customerFactory,
            $urlFactory,
            $session,
            $eventManager,
            $httpContext,
            $customerRepository,
            $groupManagement,
            $response,
            $accountConfirmation
        );
        $this->getParentCustomer = $getParentCustomer;
    }

    public function getCustomer()
    {
        $customer = parent::getCustomer();

        return $this->getParentCustomer->execute($customer->getDataModel());
    }

    public function getCustomerId(): ?string
    {
        $customer = parent::getCustomerId();

        return $this->getParentCustomer->getByCustomerId($customer) ?
            $this->getParentCustomer->getByCustomerId($customer)->getId() :
            null;
    }
}
