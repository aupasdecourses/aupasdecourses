<?php

namespace Apdc\ApdcBundle\Entity;

class Billing
{
    private $_id_billing;
    private $_date_finalized;
    private $_discount_shop_HT;
    private $_discount_shop_TVA_percent;
    private $_comments_discount_shop;
    private $_processing_fees_HT;
    private $_processing_fees_TVA_percent;

    public function __construct()
    {
        $this->_date_finalized = new \Datetime();
    }

    public function getIdBilling()
    {
        return $this->_id_billing;
    }

    public function setIdBilling($idbilling)
    {
        $this->_id_billing = $idbilling;

        return $this;
    }

    public function getDateFinalized()
    {
        return $this->_date_finalized;
    }

    public function setDateFinalized($datefinalized)
    {
        return $this->_date_finalized = $datefinalized;
    }

    public function getDiscountShopHT()
    {
        return $this->_discount_shop_HT;
    }

    public function setDiscountShopHT($discountshopHT)
    {
        return $this->_discount_shop_HT = $discountshopHT;
    }

    public function getDiscountShopTVAPercent()
    {
        return $this->_discount_shop_TVA_percent;
    }

    public function setDiscountShopTVAPercent($discountshopTVApercent)
    {
        return $this->_discount_shop_TVA_percent = $discountshopTVApercent;
    }

    public function getCommentsDiscountShop()
    {
        return $this->_comments_discount_shop;
    }

    public function setCommentsDiscountShop($commentsdiscountshop)
    {
        return $this->_comments_discount_shop = $commentsdiscountshop;
    }

    public function getProcessingFeesHT()
    {
        return $this->_processing_fees_HT;
    }

    public function setProcessingFeesHT($processingfeesHT)
    {
        return $this->_processing_fees_HT = $processingfeesHT;
    }

    public function getProcessingFeesTVAPercent()
    {
        return $this->_processing_fees_TVA_percent;
    }

    public function setProcessingFeesTVAPercent($processingfeesTVApercent)
    {
        return $this->_processing_fees_TVA_percent = $processingfeesTVApercent;
    }
}
