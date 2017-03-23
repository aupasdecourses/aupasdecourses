<?php

namespace Apdc\ApdcBundle\Entity;

class Refund
{
    private $id;

    private $originalReference;
    private $value;

    public function getId()
    {
        return $this->id;
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
}
