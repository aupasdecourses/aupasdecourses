<?php

namespace Apdc\ApdcBundle\Entity;

/**
 * NoteOrder
 */
class NoteOrder
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var float
     */
    private $orderId;

    /**
     * @var float
     */
    private $note;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orderId
     *
     * @param float $orderId
     *
     * @return NoteOrder
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return float
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set note
     *
     * @param float $note
     *
     * @return NoteOrder
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return float
     */
    public function getNote()
    {
        return $this->note;
    }
}

