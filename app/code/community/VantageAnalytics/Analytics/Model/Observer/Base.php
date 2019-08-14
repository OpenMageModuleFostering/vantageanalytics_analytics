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

        protected function collectData($entity)
        {
            if(!Mage::helper('analytics/account')->isVerified()){
                return array();
            }
            $transform = Mage::getModel("analytics/Transformer_{$this->transformer}", $entity);
            return $transform->toVantage();
        }

        public function isAdmin()
        {
            return (Mage::app()->getStore()->isAdmin() ||
                    Mage::getDesign()->getArea() == 'adminhtml');
        }

        public function performSave($observer)
        {
            if(!Mage::helper('analytics/account')->isVerified()){
                return;
            }
            try {
                $entity = $this->getEntity($observer->getEvent());
                $data = $this->collectData($entity);
                if (!empty($data)) {
                    $this->api->enqueue('create', $data);
                }
            } catch (Exception $e) {
                Mage::helper('analytics/log')->logException($e);
            }
        }

        public function performDelete($observer)
        {
            if(!Mage::helper('analytics/account')->isVerified()){
                return;
            }
            try {
                $entity = $this->getEntity($observer->getEvent());
                $data = $this->collectData($entity);
                if (!empty($data)) {
                    $this->api->enqueue('delete', $data);
                }
            } catch (Exception $e) {
                Mage::helper('analytics/log')->logException($e);
            }
        }

}
