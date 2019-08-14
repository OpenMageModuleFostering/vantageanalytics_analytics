<?php

class VantageAnalytics_Analytics_Model_Transformer_Customer extends VantageAnalytics_Analytics_Model_Transformer_Base
{

    public static function factory($magentoCustomer, $magentoStore)
    {
        return new self($magentoCustomer, $magentoStore);
    }

    public function storeIds()
    {
        return array($this->entity->getStore()->getWebsiteId());
    }

    public function externalIdentifier()
    {
        return $this->entity->getId();
    }

    public function email()
    {
        return $this->entity->getEmail();
    }

    public function firstName()
    {
        return $this->entity->getFirstname();
    }

    public function lastName()
    {
        return $this->entity->getLastname();
    }

    public function emailVerified()
    {
        return $this->entity->getConfirmation() == NULL;
    }

    public function acceptsMarketing()
    {
        $subscriber = VantageAnalytics_Analytics_Model_SubscriberInformation::factory($this->email());
        return $subscriber->acceptsMarketing();
    }

    public function company()
    {
        return $this->entity->getCompany();
    }

    public function phoneNumber()
    {
        $address = $this->entity->getDefaultAddress();
    }

    public function entityType()
    {
        return "customer";
    }

    public function toVantage()
    {

        $address =
            VantageAnalytics_Analytics_Model_AddressRetriever::factory($this->entity)
                ->address();

        $vantageAddress =
            VantageAnalytics_Analytics_Model_Transformer_Address::factory($address);

        $c = array();

        $c['entity_type']               = $this->entityType();
        $c['external_identifier']       = $this->externalIdentifier();
        $c['store_ids']                 = $this->storeIds();
        $c['source_created_at']         = $this->sourceCreatedAt();
        $c['source_updated_at']         = $this->sourceUpdatedAt();
        $c['first_name']                = $this->firstName();
        $c['last_name']                 = $this->lastName();
        $c['email']                     = $this->email();
        $c['email_verified']            = $this->emailVerified();
        $c['accepts_marketing']         = $this->acceptsMarketing();
        $c['company']                   = $this->company();
        $c["phone"]                     = $vantageAddress->telephone();
        $c["external_address_id"]       = $vantageAddress->externalIdentifier();
        $c["address_line_1"]            = $vantageAddress->addressLine1();
        $c["address_line_2"]            = $vantageAddress->addressLine2();
        $c["city"]                      = $vantageAddress->city();
        $c["province"]                  = $vantageAddress->province();
        $c["country"]                   = $vantageAddress->country();
        $c["postal_code"]               = $vantageAddress->postalCode();
        $c["province_code"]             = $vantageAddress->provinceCode();
        $c["country_code"]              = $vantageAddress->countryCode();
        $c["type"]                      = $vantageAddress->type();

        $c["addresses"] = null;

        return $c;
    }
}
