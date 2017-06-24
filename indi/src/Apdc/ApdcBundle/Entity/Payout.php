<?php

namespace Apdc\ApdcBundle\Entity;

class Payout
{
    private $id;

    private $value;
    private $iban;
    private $ownerName;
    private $reference;
    private $shopperEmail;
	private $shopperReference;

	private $date;

	public function __construct()
	{
		$this->date	= new \Datetime();
	}


    public function getId()
    {
        return $this->id;
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

    public function setIban($iban)
    {
        $this->iban = $iban;

        return $this;
    }

    public function getIban()
    {
        return $this->iban;
    }

    public function setOwnerName($ownerName)
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    public function getOwnerName()
    {
        return $this->ownerName;
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

    public function setShopperEmail($shopperEmail)
    {
        $this->shopperEmail = $shopperEmail;

        return $this;
    }

    public function getShopperEmail()
    {
        return $this->shopperEmail;
    }

    public function setShopperReference($shopperReference)
    {
        $this->shopperReference = $shopperReference;

        return $this;
    }

    public function getShopperReference()
    {
        return $this->shopperReference;
	}

	public function getDate()
	{
		return $this->date;
	}

	public function setDate($date)
	{
		$this->date = $date;

		return $this;
	}
}
