<?php
abstract class VantageAnalytics_Analytics_Model_Observer_Base
{
        public function __construct($transformer='')
        {
            $this->api = new VantageAnalytics_Analytics_Model_Api_RequestQueue();
            $this->transformer = $transformer;
        }

        // abstract protected function
        abstract protected function getEntity($event);

        protected function collectData($entity, $store)
        {
            if(!Mage::helper('analytics/account')->isVerified()){
                return array();
            }
            if (is_null($store)) {
                $store = Mage::app()->getStore();
            }
            $transformClass = "VantageAnalytics_Analytics_Model_Transformer_" . $this->transformer;
            $transform = new $transformClass($entity, $store);
            return $transform->toVantage();
        }

        public function isAdmin()
        {
            return (Mage::app()->getStore()->isAdmin() ||
                    Mage::getDesign()->getArea() == 'adminhtml');
        }

        public function performSave($observer, $store=null)
        {
            if(!Mage::helper('analytics/account')->isVerified()){
                return;
            }
            if (is_null($store)) {
                $store = Mage::app()->getStore();
            }
            try {
                $entity = $this->getEntity($observer->getEvent());
                $data = $this->collectData($entity, $store);
                if (!empty($data)) {
                    $this->api->enqueue('create', $data);
                }
            } catch (Exception $e) {
                Mage::helper('analytics/log')->logException($e);
            }
        }

        public function performDelete($observer, $store=null)
        {
            if(!Mage::helper('analytics/account')->isVerified()){
                return;
            }
            if (is_null($store)) {
                $store = Mage::app()->getStore();
            }
            try {
                $entity = $this->getEntity($observer->getEvent());
                $data = $this->collectData($entity, $store);
                if (!empty($data)) {
                    $this->api->enqueue('delete', $data);
                }
            } catch (Exception $e) {
                Mage::helper('analytics/log')->logException($e);
            }
        }

}
