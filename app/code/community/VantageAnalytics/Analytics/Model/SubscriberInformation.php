<?php
class VantageAnalytics_Analytics_Model_SubscriberInformation
{
    public static function factory($email)
    {
        return new self($email);
    }

    public function __construct($email)
    {
        $this->email = $email;
    }

    private function _subscribedStatus()
    {
        // http://docs.magentocommerce.com/Mage_Newsletter/Mage_Newsletter_Model_Subscriber.html
        // STATUS_NOT_ACTIVE      = 2
        // STATUS_SUBSCRIBED      = 1
        // STATUS_UNCONFIRMED     = 4
        // STATUS_UNSUBSCRIBED    = 3

        return 1;
    }

    public function subscriber()
    {
        return Mage::getModel('newsletter/subscriber')
            ->loadByEmail($this->email);
    }

    public function status()
    {
        return $this->subscriber()->getStatus();

    }

    public function acceptsMarketing()
    {
        return $this->status() == $this->_subscribedStatus();
    }

}
