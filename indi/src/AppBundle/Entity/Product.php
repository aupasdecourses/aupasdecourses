<?php
namespace AppBundle\Entity;

use Apdc\ApdcBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

use \AutoBundle\Entity\UploadTrait;

/**
 * @ORM\Table(name="api_product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Product
{
    use UploadTrait;

    /** Upload configuration */
    private $config = [
        'upload' => [
            'rootDir'   => '/../../../web/',
            'uploadDir' => 'uploads/products/',
            /* 'photo' => [
                'thumbnail' => [
                    'width'  => 150,
                    'height' => 100
                ],
                'resize' => [
                    'width'  => 250,
                    'height' => 250
                ]
            ] */
        ]
    ];

    private $uploadFiles = ['photo'];

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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="The name cannot be empty")
     */
    private $name;

    /**
     * Avalaible on APDC
     *
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $available = true;

    /**
     * Slected on APDC
     *
     * @var boolean
     *
     * @ORM\Column(name="on_selection", type="boolean")
     */
    private $selected = false;

    /**
     * Price
     *
     * @var float
     *
     * @ORM\Column(name="prix_public", type="float", length=255)
     * @Assert\NotBlank(message="The price cannot be empty")
     */
    private $price;

    /**
     * Type of price (per kg, per unit)
     *
     * @var string
     *
     * @ORM\Column(name="unite_prix", type="integer", length=11)
     */
    private $priceUnit = 1;

    /**
     * @var array
     */
    private $priceUnitValues = [
        1 => 'Kg',
        2 => 'Unit'
    ];

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
     * @ORM\Column(name="poids_portion", type="float", length=255)
     */
    private $portionWeight = 500;

    /**
     * Number of portion
     *
     * @var string
     *
     * @ORM\Column(name="nbe_portion", type="integer", length=11)
     */
    private $portionNumber = 1;

    /**
     * VAT
     *
     * @var string
     *
     * @ORM\Column(name="tax_class_id", type="integer", length=11)
     */
    private $tax = 1;

    /**
     * @var array
     */
    private $taxValues = [
        1 => '5.5',
        2 => '10',
        3 => '20'
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
     * @ORM\Column(name="produit_bio", type="boolean")
     */
    private $bio = false;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @Assert\File(
     *      maxSize = "10M",
     *      mimeTypes = {"image/jpeg", "image/pjpeg", "image/png", "image/x-png", "image/gif"},
     *      mimeTypesMessage = "Only images are accepted as photo.",
     *      uploadIniSizeErrorMessage = "The photo image file is too big (10Mo max).",
     *      uploadFormSizeErrorMessage = "The photo image file is too big (10Mo max).",
     *      uploadErrorMessage = "The photo image file cannot be transfered.",
     *      maxSizeMessage = "The photo image file is too big (10Mo max)."
     * )
     *
     * @var UploadedFile $photoFile
     */
    public $photoFile;

    /**
     * The merchant user
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Apdc\ApdcBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Sets photoFile.
     *
     * @param UploadedFile $file
     */
    public function setPhotoFile(UploadedFile $file = null)
    {
        $this->photoFile = $file;

        if (isset($this->photo))
        {
            // store the old name to delete after the update
            $this->tempPhoto = $this->photo;
            $this->photo     = null;
        }
        else
        {
            $this->photo = 'initial';
        }
    }

    /**
     * Get logoFile.
     *
     * @return UploadedFile
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Product
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
