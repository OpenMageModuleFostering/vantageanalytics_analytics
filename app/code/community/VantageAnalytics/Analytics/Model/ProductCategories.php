<?php
class VantageAnalytics_Analytics_Model_ProductCategories
{
    public static function factory($product)
    {
        return new self($product);
    }

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function categories()
    {
        $categories = array();

        foreach ($this->_categoryIds() as $catId) {
            $categories[$catId] = $this->_getCategoryName($catId);
        }

        return $categories;
    }

    private function _categoryIds()
    {
        return $this->product->getCategoryIds();
    }

    private function _getCategoryName($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);

        return $category->getName();
    }

}
