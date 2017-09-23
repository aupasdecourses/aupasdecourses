<?php

namespace Apdc\ApdcBundle\Entity;

class PayoutChoice
{
    private $id;

    private $choice;

    public function getId()
    {
        return $this->id;
    }

    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }

    public function getChoice()
    {
        return $this->choice;
    }
}

