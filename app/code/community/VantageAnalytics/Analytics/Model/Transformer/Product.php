<?php
class VantageAnalytics_Analytics_Model_Transformer_Product extends VantageAnalytics_Analytics_Model_Transformer_Base
{
    public static function factory($magentoProduct)
    {
        return new VantageAnalytics_Analytics_Model_Transformer_Product($magentoProduct);
    }

    public function entityType()
    {
        return "product";
    }

    public function storeIds()
    {
        return $this->entity->getWebsiteIds();
    }

    public function externalIdentifier()
    {
        return $this->entity->getId();
    }

    public function externalParentIdentifier()
    {
        return VantageAnalytics_Analytics_Model_ParentProduct::factory($this->entity)
            ->id();
    }

    public function name()
    {
        return $this->entity->getName();
    }

    public function type()
    {
        return $this->entity->getTypeId();
    }

    public function sku()
    {
        return $this->entity->getSku();
    }

    public function taxable()
    {
        return $this->entity->getTaxClassId() == 2;
    }

    public function availability()
    {
        return $this->entity->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
    }

    public function price()
    {
        return $this->entity->getPrice();
    }

    public function weight()
    {
        return $this->entity->getWeight();
    }

    public function shippingRequired()
    {
        return $this->type() != Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL;
    }

    public function quantity()
    {
        return Mage::getModel('cataloginventory/stock_item')
            ->loadByProduct($this->externalIdentifier())
            ->getQty();
    }

    public function productUrl()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            // Reload the product in the context of the store
            // to get the correct url for the product
            $websites = Mage::app()->getWebsites();
            $storeId = $websites[1]->getDefaultStore()->getId();
            $product = Mage::helper('catalog/product')->getProduct(
                $this->externalIdentifier(),
                $storeId
            );

            return empty($product) ? NULL: $product->getProductUrl();
        }

        return $this->entity->getProductUrl();
    }

    public function imageUrls()
    {
        $images = VantageAnalytics_Analytics_Model_ProductImages::factory($this->entity);
        return $images->urls();
    }

    public function categories()
    {
        return VantageAnalytics_Analytics_Model_ProductCategories::factory($this->entity)
            ->categories();
    }

    public function toVantage()
    {
        $product = Array();

        $product['store_ids']                       = $this->storeIds();
        $product['external_identifier']             = $this->externalIdentifier();
        $product['external_parent_identifier']      = $this->externalParentIdentifier();
        $product['source_created_at']               = $this->sourceCreatedAt();
        $product['source_updated_at']               = $this->sourceUpdatedAt();
        $product['name']                            = $this->name();
        $product['type']                            = $this->type();
        $product['sku']                             = $this->sku();
        $product['taxable']                         = $this->taxable();
        $product['availability']                    = $this->availability();
        $product['price']                           = $this->price();
        $product['weight']                          = $this->weight();
        $product['requires_shipping']               = $this->shippingRequired();
        $product['quantity']                        = $this->quantity();
        $product['entity_type']                     = "product";
        $product['images']                          = $this->imageUrls();
        $product['categories']                      = $this->categories();
        $product['url']                             = $this->productUrl();

        return $product;
    }
}
