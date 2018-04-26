<?php

namespace Apdc\ApdcBundle\Entity;

class Comment
{

	private $type;
	private $text;
	private $order_id;
	private $merchant_id;
	private $associated_order_id;

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getText()
	{
		return $this->text;
	}

	public function setText($text)
	{
		$this->text = $text;
	}

	public function getOrderId()
	{
		return $this->order_id;
	}

	public function setOrderId($order_id)
	{
		$this->order_id = $order_id;
	}

	public function getMerchantId()
	{
		return $this->merchant_id;
	}

	public function setMerchantId($merchant_id)
	{
		$this->merchant_id = $merchant_id;
	}

	public function getAssociatedOrderId()
	{
		return $this->associated_order_id;
	}

	public function setAssociatedOrderId($associated_order_id)
	{
		$this->associated_order_id = $associated_order_id;
	}
}