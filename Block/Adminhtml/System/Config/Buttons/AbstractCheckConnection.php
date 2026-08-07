<?php

namespace Commerce365\Core\Block\Adminhtml\System\Config\Buttons;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

abstract class AbstractCheckConnection extends Field
{
    protected $_template = 'Commerce365_Core::system/config/buttons/checkconnection.phtml';
    abstract public function getRedirectUrl(): string;

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    public function getButtonHtml(): string
    {
        $redirectUrl = $this->getRedirectUrl();
        $store = $this->_request->getParam('store');
        $website = $this->_request->getParam('website');
        if ($store) {
            $redirectUrl .= "store/$store";
        } elseif ($website) {
            $redirectUrl .= "website/$website";
        }
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id'      => 'Test button',
                'label'   => __('Test Connection'),
                'onclick' => 'window.location="' . $redirectUrl . '";',
            ]
        );

        return $button->toHtml();
    }
}
