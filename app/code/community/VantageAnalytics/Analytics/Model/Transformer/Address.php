<?php

class VantageAnalytics_Analytics_Model_Transformer_Address
{
    public function __construct($magentoAddress, $addressType='')
    {
        $this->address = $magentoAddress;
        $this->addressType = $addressType;
    }

    public static function factory($magentoAddress, $addressType='')
    {
        return new self($magentoAddress, $addressType);
    }

    public function externalIdentifier()
    {
        return $this->address->getId();
    }

    public function addressLine1()
    {
        return $this->address ? $this->address->getStreet1() : '';
    }

    public function addressLine2()
    {
        return $this->address ? $this->address->getStreet2() : '';
    }

    public function city()
    {
        return $this->address ? $this->address->getCity() : '';
    }

    public function province()
    {
        // Note to self, get region returns the name
        // when called on a customer/address model.
        return $this->address ? $this->address->getRegion() : '';
    }

    public function provinceCode()
    {
        return $this->address ? $this->address->getRegionCode() : '';
    }

    protected function countryModel()
    {
        if($this->address) {
            return $this->address->getCountryModel();
        }

        return '';
    }

    public function country()
    {
        if($this->address) {
            return $this->countryModel()->getName();
        }

        return '';

    }

    public function countryCode()
    {
        if($this->address) {
            return $this->countryModel()->getCountryId();
        }

        return '';
    }

    public function telephone()
    {
        return $this->address ? $this->address->getTelephone() :'';
    }

    public function postalCode()
    {
        return $this->address ? $this->address->getPostcode() : '';
    }

    public function company()
    {
        return $this->address ? $this->address->getCompany() : '';
    }

    public function type()
    {
        return $this->addressType;
    }

    public function entityType()
    {
        return "address";
    }

    public function toVantage()
    {
        $prefix = '';
        if ($this->type()) {
            $prefix = $this->type() . "_";
        }

        $data = array(
            "{$prefix}address_line_1" => $this->addressLine1(),
            "{$prefix}address_line_2" => $this->addressLine2(),
            "{$prefix}city" => $this->city(),
            "{$prefix}province" => $this->province(),
            "{$prefix}country" => $this->country(),
            "{$prefix}postal_code" => $this->postalCode(),
            "{$prefix}province_code" => $this->provinceCode(),
            "{$prefix}country_code" => $this->countryCode(),
            "{$prefix}company" => $this->company(),
        );

        return $data;
    }
}
