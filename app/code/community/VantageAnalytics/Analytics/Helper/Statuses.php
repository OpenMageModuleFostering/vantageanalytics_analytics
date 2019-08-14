<?php

class VantageAnalytics_Analytics_Helper_Statuses
{
    /*
    * see: https://github.com/OpenMage/magento-mirror/blob/magento-1.7/app/code/core/Mage/Sales/Model/Order/Item.php#L194
    * see: https://github.com/OpenMage/magento-mirror/blob/magento-1.7/app/code/core/Mage/Sales/Model/Order.php#L334
    * see: http://www.magentocommerce.com/wiki/2_-_magento_concepts_and_architecture/order_management
    */

    public static function factory($salesEntity)
    {
        return new self($salesEntity);
    }

    public function __construct($salesEntity)
    {
        $this->salesEntity = $salesEntity;
    }

    public function paymentStatus()
    {
        $state = $this->_getOrderState();

        if($state == Mage_Sales_Model_Order::STATUS_FRAUD) {
            return 'paid';
        }

        $due = $this->salesEntity->getTotalDue();

        if ($due == 0) {
            return 'paid';
        }

        return 'unpaid';
    }

    public function fulfillmentStatus()
    {
        $statuses = array();

        foreach ($this->salesEntity->getAllVisibleItems() as $item) {
            $statuses[] = $item->getStatusId();
        }

        if ($this->_allItemsShipped($statuses)) {
            return 'fulfilled';
        }

        if ($this->_partiallyFulfilled($statuses)) {
            return 'partial-fulfilled';
        }

        return 'unfulfilled';
    }

    private function _allItemsShipped($statuses)
    {
        $unique_statuses = array_unique($statuses);

        return (count($unique_statuses) == 1 && ($unique_statuses[0] == Mage_Sales_Model_Order_Item::STATUS_SHIPPED));
    }

    private function _partiallyFulfilled($statuses)
    {
        $unique_statuses = array_unique($statuses);

        /* If one line item is fully fulfilled, while others are not
         * then the order is also only partially fulfilled
        */
        return in_array(Mage_Sales_Model_Order_Item::STATUS_PARTIAL, $unique_statuses) ||
            (in_array(Mage_Sales_Model_Order_Item::STATUS_SHIPPED, $unique_statuses) && (count($unique_statuses) > 1));
    }

    public function orderStatus()
    {
        $state = $this->_getOrderState();
        switch ($state)
        {
            case Mage_Sales_Model_Order::STATE_CLOSED:
            case Mage_Sales_Model_Order::STATE_COMPLETE:
                return 'closed';
            case Mage_Sales_Model_Order::STATE_CANCELED:
            case Mage_Sales_Model_Order::STATUS_FRAUD:
                return 'canceled';
            case Mage_Sales_Model_Order::STATE_NEW:
            case Mage_Sales_Model_Order::STATE_HOLDED:
            case Mage_Sales_Model_Order::STATE_PENDING_PAYMENT:
            case Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW:
            case Mage_Sales_Model_Order::STATE_PENDING_PAYMENT:
            case Mage_Sales_Model_Order::STATE_PROCESSING:
                return 'placed';
            default:
                return 'open';
        }
    }

    private function _getOrderState()
    {
        return $this->salesEntity->getState();
    }
}
