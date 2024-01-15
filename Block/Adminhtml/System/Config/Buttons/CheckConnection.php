<?php

namespace Commerce365\Core\Block\Adminhtml\System\Config\Buttons;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CheckConnection extends Field
{
    protected $_template = 'Commerce365_Core::system/config/buttons/checkconnection.phtml';

    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    public function getAjaxUrl(): string
    {
        return $this->getUrl('commerce365_configuration/connection/check');
    }

    public function getButtonHtml(): string
    {
        $data = [
            'id'    => 'checkconnection_button',
            'label' => __('Run connection test'),
        ];

        /** @var \Magento\Backend\Block\Widget\Button $button */
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData($data);
        $button->setDataAttribute(
            [
                'mage-init' => '{"Commerce365_Core/js/system/config/buttons/checkconnection": {
                            "submitUrl":"' . $this->getAjaxUrl() . '"
                        }
                    }',
            ]
        );

        return $button->toHtml();
    }
}
