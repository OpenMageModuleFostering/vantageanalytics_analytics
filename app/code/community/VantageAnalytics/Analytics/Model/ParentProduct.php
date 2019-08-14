<?php
class VantageAnalytics_Analytics_Model_ParentProduct
{
    public static function factory($product)
    {
        return new self($product);
    }

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function id()
    {
        // Configurable products do not have parent products
        // hence, there's no ParentProduct->id()
        if ($this->_configurableProduct($this->product)) {
            return NULL;
        }

        $parentProduct = $this->parent();

        return $parentProduct ? $parentProduct->getId() : NULL;
    }

    public function parent()
    {
        // A parent is the configurable product related to a simple product
        // A simple product can have multiple parents
        // Parents can be "grouped products" or "configurable products"
        // Only one of those parents can be a "configurable product"
        // An example of a grouped product "Buy a phone and a car charger"
        $parents = $this->_parents();

        foreach ($parents as $parent)
        {
            if ($this->_configurableProduct($parent))
            {
                return $parent;
            }
        }

        return NULL;
    }

    private function _parents()
    {
        $parents = array();

        $parentIds = $this->_parentIds();

        foreach ($parentIds as $parentId) {
            $parents[] = Mage::getModel('catalog/product')->load($parentId);
        }

        return $parents;
    }

    private function _parentIds()
    {
        return Mage::getModel('catalog/product_type_configurable')
            ->getParentIdsByChild($this->product->getId());
    }

    private function _configurableProduct($product)
    {
        return ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
    }
}
