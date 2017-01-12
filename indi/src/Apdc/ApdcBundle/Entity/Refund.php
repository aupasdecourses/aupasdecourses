<?php

namespace Apdc\ApdcBundle\Entity;

class Refund
{
    private $id;

    private $merchantAccount;
    private $originalReference;
    private $value;
    private $reference;

    public function getId()
    {
        return $this->id;
    }

    public function setMerchantAccount($merchantAccount)
    {
        $this->merchantAccount = $merchantAccount;

        return $this;
    }

    public function getMerchantAccount()
    {
        return $this->merchantAccount;
    }

    public function setOriginalReference($originalReference)
    {
        $this->originalReference = $originalReference;

        return $this;
    }

    public function getOriginalReference()
    {
        return $this->originalReference;
    }


    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    public function getReference()
    {
        return $this->reference;
    }
}

