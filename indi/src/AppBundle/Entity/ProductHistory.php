<?php
namespace AppBundle\Entity;

use Apdc\ApdcBundle\Entity\User as UserBase;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="api_product_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductHistoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ProductHistory
{
    /**
     * Autoincrement ID
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Unique SKU of the product
     *
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=255, nullable=true)
     */
    private $sku;

    /**
     * Merchant reference
     *
     * @var string
     *
     * @ORM\Column(name="reference_interne_magasin", type="string", length=255, nullable=true)
     */
    private $ref;

    /**
     * Name of the product
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * Avalaible on APDC
     *
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $available = true;

    /**
     * Slected on APDC
     *
     * @var boolean
     *
     * @ORM\Column(name="on_selection", type="boolean", nullable=true)
     */
    private $selected = false;

    /**
     * Price
     *
     * @var float
     *
     * @ORM\Column(name="prix_public", type="float", length=255, nullable=true)
     */
    private $price;

    /**
     * Type of price (per kg, per unit)
     *
     * @var string
     *
     * @ORM\Column(name="unite_prix", type="string", length=11, nullable=true)
     */
    private $priceUnit;

    /**
     * A short description of the product
     *
     * @var string
     *
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     */
    private $shortDescription;

    /**
     * Wieght of a portion (in Kg)
     *
     * @var string
     *
     * @ORM\Column(name="poids_portion", type="float", length=255, nullable=true)
     */
    private $portionWeight = 500;

    /**
     * Number of portion
     *
     * @var string
     *
     * @ORM\Column(name="nbre_portion", type="integer", length=11, nullable=true)
     */
    private $portionNumber = 1;

    /**
     * VAT
     *
     * @var string
     *
     * @ORM\Column(name="tax_class_id", type="integer", length=11, nullable=true)
     */
    private $tax = 5;

    /**
     * @var array
     */
    private $taxValues = [
        5  => '5.5',
        9  => '10',
        10 => '20'
    ];

    /**
     * Origin of the product
     *
     * @var string
     *
     * @ORM\Column(name="origine", type="string", length=255, nullable=true)
     */
    private $origin;

    /**
     * Bio product
     *
     * @var string
     *
     * @ORM\Column(name="produit_biologique", type="boolean", nullable=true)
     */
    private $bio = false;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="shop_id", type="integer", length=11, nullable=true)
     */
    private $shopId;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdOn", type="datetime")
     */
    private $createdOn;

    /**
     * @var UserBase
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Apdc\ApdcBundle\Entity\UserBase")
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * Object to Array, used for export
     *
     * @return array
     */
    public function toArray()
    {
        return (array) $this;
    }

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
     * Set sku
     *
     * @param string $sku
     *
     * @return ProductHistory
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set ref
     *
     * @param string $ref
     *
     * @return ProductHistory
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductHistory
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
     * Set available
     *
     * @param boolean $available
     *
     * @return ProductHistory
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return boolean
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     *
     * @return ProductHistory
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return ProductHistory
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set priceUnit
     *
     * @param float $priceUnit
     *
     * @return ProductHistory
     */
    public function setPriceUnit($priceUnit)
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    /**
     * Get priceUnit
     *
     * @return float
     */
    public function getPriceUnit()
    {
        return $this->priceUnit;
    }

    /**
     * Get priceUnitValue
     *
     * @return float
     */
    public function getPriceUnitValue()
    {
        if ($this->priceUnit) {
            return $this->priceUnitValues[$this->priceUnit];
        }

        return null;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     *
     * @return ProductHistory
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set portionWeight
     *
     * @param float $portionWeight
     *
     * @return ProductHistory
     */
    public function setPortionWeight($portionWeight)
    {
        $this->portionWeight = $portionWeight;

        return $this;
    }

    /**
     * Get portionWeight
     *
     * @return float
     */
    public function getPortionWeight()
    {
        return $this->portionWeight;
    }

    /**
     * Set portionNumber
     *
     * @param integer $portionNumber
     *
     * @return ProductHistory
     */
    public function setPortionNumber($portionNumber)
    {
        $this->portionNumber = $portionNumber;

        return $this;
    }

    /**
     * Get portionNumber
     *
     * @return integer
     */
    public function getPortionNumber()
    {
        return $this->portionNumber;
    }

    /**
     * Set tax
     *
     * @param integer $tax
     *
     * @return ProductHistory
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return integer
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Get taxValue
     *
     * @return float
     */
    public function getTaxValue()
    {
        if ($this->tax) {
            return $this->taxValues[$this->tax];
        }

        return null;
    }

    /**
     * Set origin
     *
     * @param string $origin
     *
     * @return ProductHistory
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set bio
     *
     * @param boolean $bio
     *
     * @return ProductHistory
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get bio
     *
     * @return boolean
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return ProductHistory
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set bio
     *
     * @param integer $shopId
     *
     * @return ProductHistory
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * Get bio
     *
     * @return boolean
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ProductHistory
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set createdBy
     *
     * @param UserBase $createdBy
     *
     * @return ProductHistory
     */
    public function setCreatedBy(UserBase $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return UserBase
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
