<?php

class VantageAnalytics_Analytics_Block_Adminhtml_Reset extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $resetUrl = $this->getUrl('adminhtml/analytics_reset/reset');
        $this->setElement($element);
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Reset')
            ->setOnClick("setLocation('{$resetUrl}')")
            ->toHtml();
    }
}
