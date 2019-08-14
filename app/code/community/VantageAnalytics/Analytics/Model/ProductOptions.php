<?php
class VantageAnalytics_Analytics_Model_ProductOptions
{
    public static function factory($lineItem)
    {
        return new self($lineItem);
    }

    public function __construct($lineItem)
    {
        $this->lineItem = $lineItem;
        $this->product = $this->lineItem->getProduct();
    }

    public function _configurableProduct()
    {
        $type = $this->product->getTypeID();
        return ($type == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
    }

    public function options()
    {
        // Only configurable products have options
        // If we dont have product options, send NULL
        // PHP converts empty dictionaries to arrays
        // and with forceObject set, it breaks other things
        if (!$this->_configurableProduct()) {
            return NULL;
        }

        $attributes = $this->_attributes();
        $options = array();
        foreach ($attributes as $option) {
            $options += $this->_buildSelection($option);
        }

        return empty($options) ? NULL : $options;
    }

    private function _attributes() {
        $attributes = $this->lineItem->getProductOptions();
        return $attributes['attributes_info'];
    }

    private function _buildSelection($option) {
        $values = array_map("strtolower", array_values($option));
        return array($values[0] => $values[1]);
    }

}
