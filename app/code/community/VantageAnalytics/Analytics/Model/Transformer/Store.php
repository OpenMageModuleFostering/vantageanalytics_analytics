<?php

class VantageAnalytics_Analytics_Model_Transformer_Store extends VantageAnalytics_Analytics_Model_Transformer_Base
{

    public function __construct($magentoWebsite)
    {
        $this->website = $magentoWebsite;
        $this->entity = $magentoWebsite->getDefaultStore();
    }

    public static function factory($magentoWebsite)
    {
        return new self($magentoWebsite);
    }

    public function sourceCreatedAt()
    {
        $config = Mage::app()->getConfig();

        return VantageAnalytics_Analytics_Helper_DateFormatter::factory(
            $config->getNode('global/install/date')
        )->toDate();

    }

    public function sourceUpdatedAt()
    {
        return date("c");
    }

    public function storeId()
    {
        return $this->website->getId();
    }

    public function storeIds()
    {
        return array($this->storeId());
    }

    public function externalIdentifier()
    {
        return $this->entity->getId();
    }

    public function domain()
    {
        return $this->entity->getBaseUrl();
    }

    public function name()
    {
        return $this->website->getName();
    }

    public function countryCode()
    {
        // general settings apply to stores/websites in installation
        return Mage::getStoreConfig('general/country/default', $this->entity->getId());
    }

    public function country()
    {
        $code = $this->countryCode();

        return Mage::getModel('directory/country')
            ->loadByCode($code)
            ->getName();
    }

    public function currencyCode()
    {
        return $this->entity->getCurrentCurrencyCode();
    }

    public function readOnly()
    {
        // Would map to "password enabled"
        return $this->entity->isReadOnly();
    }

    public function localeCode()
    {
        $locale = Mage::app()->getLocale();

        return $locale->getLocaleCode();
    }

    public function language()
    {
        $locale = Mage::app()->getLocale();

        return $locale->getLocaleCode();
    }

    public function timezone()
    {
        $locale = Mage::app()->getLocale();

        return $locale->getTimezone();
    }

    public function addressLine()
    {
        // Magento uses a free form address field for the store
        return Mage::getStoreConfig('general/store_information/address', $this->storeId());
    }

    public function shopOwnerPhone()
    {
        return Mage::getStoreConfig('general/store_information/phone', $this->storeId());
    }

    public function shopOwnerEmail()
    {
        // Best guess is that this is the store's email address
        return Mage::getStoreConfig('trans_email/ident_general/email', $this->storeId());
    }

    public function supportEmail()
    {
        return Mage::getStoreConfig('trans_email/ident_support/email', $this->storeId());
    }

    public function shopOwnerName()
    {
        return Mage::getStoreConfig('trans_email/ident_general/name', $this->storeId());
    }

    public function entityType()
    {
        return "store";
    }

    public function toVantage()
    {
        $methods = array_diff(
            get_class_methods($this),
            array('toVantage', '__construct', 'factory')
        );
        $data = array();
        foreach ($methods as $method) {
            $attr = strtolower(preg_replace(
               '/(?<=\\w)(?=[A-Z])/',"_$1", $method));
            $data[$attr] = call_user_func(array($this, $method));
        }
        return $data;
    }
}
