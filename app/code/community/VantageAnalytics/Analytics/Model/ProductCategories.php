<?php

class VantageAnalytics_Analytics_Model_ProductCategories
{
    private static $_product_cache = array();

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
        if (array_key_exists($categoryId, self::$_product_cache)) {
            return self::$_product_cache[$categoryId];
        }

        $category = Mage::getModel('catalog/category')->load($categoryId);

        self::$_product_cache[$categoryId] = $category->getName();
        return self::$_product_cache[$categoryId];
    }

}
