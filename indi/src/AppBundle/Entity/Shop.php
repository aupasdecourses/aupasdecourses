<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Note: Magento Table
 *
 * @ORM\Table(name="apdc_shop", options={"engine"="InnoDB"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShopRepository")
 */
class Shop
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The merchant ID
     *
     * @var string
     *
     * @ORM\Column(name="id_commercant", type="integer", length=11)
     */
    private $merchant;

    /**
     * Shop name
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * SKU Code
     *
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * SKU incremental number
     *
     * @var integer
     *
     * @ORM\Column(name="incremental", type="integer", length=11)
     */
    private $incremental;

    /**
     * The product merchant ID
     *
     * @var string
     *
     * @ORM\Column(name="id_attribut_commercant", type="integer", length=11)
     */
    private $productMerchant;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set merchant
     *
     * @param integer $merchant
     *
     * @return Shop
     */
    public function setMerchant($merchant)
    {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * Get merchant
     *
     * @return integer
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Shop
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Shop
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set incremental
     *
     * @param integer $incremental
     *
     * @return Shop
     */
    public function setIncremental($incremental)
    {
        $this->incremental = $incremental;

        return $this;
    }

    /**
     * Get incremental
     *
     * @return integer
     */
    public function getIncremental()
    {
        return $this->incremental;
    }

    /**
     * Set productMerchant
     *
     * @param integer $productMerchant
     *
     * @return Shop
     */
    public function setProductMerchant($productMerchant)
    {
        $this->productMerchant = $productMerchant;

        return $this;
    }

    /**
     * Get productMerchant
     *
     * @return integer
     */
    public function getProductMerchant()
    {
        return $this->productMerchant;
    }
}
