<?php

class VantageAnalytics_Analytics_Helper_Checkout extends Mage_Core_Helper_Abstract
{
    protected function getCustomerFields($order)
    {
        $customerFields = array();

        try {
            $customerFields['customerEmail'] = $order->getCustomerEmail();
            $customerFields['customerFirstName'] = $order->getCustomerFirstname();
        } catch (Exception $e) {
            // pass
        }

        return $customerFields;
    }

    protected function getBillingAddressFields($order)
    {
        $addressFields = array();
        try {
            $address = $order->getBillingAddress();
            if ($address) {
                $addressFields['addressLine'] = $address->getStreet1();
                $addressFields['city'] = $address->getCity();
                $addressFields['state'] = $address->getRegion();
                $addressFields['postalCode'] = $address->getPostcode();
                $country = $address->getCountryModel() ? $address->getCountryModel()->getName() : '';
                $addressFields['country'] = $country;
            } else {
                return array();
            }
        } catch (Exception $e) {
            // pass
        }
        return array('customerBillingAddress' => $addressFields);
    }

    protected function getOrderFields($order)
    {
        $orderFields = array();

        try {
            $orderFields['totalPrice'] = round($order->getGrandTotal(), 2);
            $orderFields['totalTax'] = round($order->getTaxAmount(), 2);
            $orderFields['totalShipping'] = round($order->getShippingAmount(), 2);
        } catch (Exception $e) {
            return $orderFields;
        }
        return $orderFields;
    }

    protected function getOrderLineItems($order)
    {
        $vantageItems = array();

        try {
            $orderItems = $order->getAllVisibleItems();

            foreach ($orderItems as $item) {
                $product = $item->getProduct();
                $vantageItems[] = array(
                    'sku' => $item->getData('product_id'),
                    'name' => $item->getName(),
                    'category' => null, // Maybe in the future.
                    'price' => round($item->getOriginalPrice(), 2),
                    'quantity' => (int) round($item->getQtyOrdered(), 0),
                );
            }
        } catch (Exception $e) {
            // pass
        }

        return array('lineItems' => $vantageItems);
    }

    public function createVantageCheckout($orderId)
    {
        $vantageCheckout = array('orderId' => $orderId);
        try {
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order) {
                return array();
            }

            $vantageCheckout = array_merge($vantageCheckout, $this->getOrderFields($order));
            $vantageCheckout = array_merge($vantageCheckout, $this->getBillingAddressFields($order));
            $vantageCheckout = array_merge($vantageCheckout, $this->getCustomerFields($order));
            $vantageCheckout = array_merge($vantageCheckout, $this->getOrderLineItems($order));
        } catch (Exception $e) {
            // pass
        }
        return $vantageCheckout;
    }
}
