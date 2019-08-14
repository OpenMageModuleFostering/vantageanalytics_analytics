<?php

abstract class VantageAnalytics_Analytics_Model_Transformer_Base
{
    /* Magento returns the "created_at" in the store's
     * timezone thus, it needs to be converted to UTC.
     * 'updated_at' is already in UTC, and does not
     * require conversion. Neither are formatted correctly.
     */
    public function __construct($magentoEntity, $magentoStore)
    {
        $this->entity = $magentoEntity;
        $this->data = $this->entity->getData();
        $this->magentoStore = $magentoStore;
    }

    public function sourceCreatedAt()
    {
        $name = 'created_at';

        if (isset($this->data[$name])) {
            return VantageAnalytics_Analytics_Helper_DateFormatter::factory(
                $this->data[$name]
            )->toUtcDate();
        }

        return NULL;
    }

    public function sourceUpdatedAt()
    {
        $name = 'updated_at';

        if (isset($this->data[$name])) {
            return VantageAnalytics_Analytics_Helper_DateFormatter::factory(
                $this->data[$name]
            )->toDate();
        }

        return NULL;
    }

    public function siteId()
    {
        return Mage::helper('analytics/account')->getSiteId();
    }

    public abstract function entityType();
    public abstract function toVantage();
}
