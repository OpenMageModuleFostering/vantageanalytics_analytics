<?php
class VantageAnalytics_Analytics_Model_Transformer_Product extends VantageAnalytics_Analytics_Model_Transformer_Base
{
    public static function factory($magentoProduct, $magentoStore)
    {
        return new VantageAnalytics_Analytics_Model_Transformer_Product($magentoProduct, $magentoStore);
    }

    public function entityType()
    {
        return "product";
    }

    public function storeIds()
    {
        return array($this->magentoStore->getWebsiteId());
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
        $url = Mage::helper('catalog/product')->getProductUrl($this->entity->getId());
        $pos = strpos($url, '?');
        $url = ($pos > 0) ? substr($url, 0, $pos) : $url;
        return $url;
    }

    public function imageUrls()
    {
        $images = VantageAnalytics_Analytics_Model_ProductImages::factory($this->entity, $this->magentoStore);
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
        $product['site_id']                         = $this->siteId();
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
