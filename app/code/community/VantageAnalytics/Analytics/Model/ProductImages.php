<?php
class VantageAnalytics_Analytics_Model_ProductImages
{
    public static function factory($product, $store)
    {
        return new self($product, $store);
    }

    public function __construct($product, $store)
    {
        $this->product = $product;
        $this->config  = $product->getMediaConfig();
        $this->store   = $store;
    }

    public function urls()
    {
        return array(
            "base"      => $this->_imageUrl($this->_baseImage()),
            "small"     => $this->_imageUrl($this->_smallImage()),
            "thumbnail" => $this->_imageUrl($this->_thumbnail())
        );
    }

    private function _imageUrl($image)
    {
        return $image ? $this->config->getMediaUrl($image) : NULL;
    }

    private function _baseImage()
    {
        return $this->product->getImage();
    }

    private function _smallImage()
    {
        return $this->product->getSmallImage();
    }

    private function _thumbnail()
    {
        return $this->product->getThumbnail();
    }

}
