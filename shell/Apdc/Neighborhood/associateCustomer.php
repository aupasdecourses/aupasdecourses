<?php

require_once '../../abstract.php';
 
class Apdc_Neighborhood_Shell_AssociateCustomer extends Mage_Shell_Abstract
{
    protected $neighborhoodsByWebsiteIds = array();
    protected $neighborhoodByPostcode = array();

    public function __construct()
    {
        parent::__construct();
    }
 
    // Shell script point of entry
    public function run()
    {
        echo '########################' . "\n";
        echo 'Associate Neighborhood To Customers' . "\n";
        echo '########################' . "\n";

        $customers = Mage::getModel('customer/customer')->getCollection();
        $nbCustomers = $customers->count();
        $nbCustomersOK = 0;
        $nbCustomersFirstNeighborhood = 0;
        $customersNoNeighborhoods = array();
        $cpt = 1;
        foreach ($customers as $customer) {
            if (!$customer->getCustomerNeighborhood()) {
                echo $cpt . '/' . $nbCustomers . ' - Get neighborhood for customer #' . $customer->getId() . ' / ' . $customer->getEmail() . "\n";
                $neighborhoods = $this->getNeighborhoodsByWebsiteId($customer->getWebsiteId());
                echo 'nb neighborhood : ' . $neighborhoods->count() . "\n";

                if ($neighborhoods->count() == 1) {
                    echo '--- Setting neighborhood ' . $neighborhoods->getFirstItem()->getName() . "\n";
                    $customer->setCustomerNeighborhood($neighborhoods->getFirstItem()->getId())->save();
                    $nbCustomersOK++;
                } else if ($neighborhoods->count() > 1) {
                    $error = false;

                    $postcode = $this->getCustomerPostcode($customer);
                    echo '--- Postcode : ' . $postcode . "\n";
                    if ($postcode) {
                        $neighborhood = $this->getNeighborhoodByPostcode($postcode);
                        if ($neighborhood && $neighborhood->getId()) {
                            echo '--- Setting neighborhood ' . $neighborhood->getName() . "\n";
                            $customer->setCustomerNeighborhood($neighborhood->getId())->save();
                            $nbCustomersOK++;
                        }
                        echo '--- Unable to retreive neighborhood by postcode' . "\n";
                        $error = true;
                    } else {
                        echo '--- No postcode found' . "\n";
                        $error = true;
                    }
                    if ($error) {
                        $customer->setCustomerNeighborhood($neighborhoods->getFirstItem()->getId())->save();
                        $nbCustomersFirstNeighborhood++;
                        echo '--- Setting first neighborhood ' . $neighborhoods->getFirstItem()->getName() . "\n";
                    }
                } else {
                    echo '--- No neighborhood available' . "\n";
                    $customersNoNeighborhoods[] = $customer;
                }
            }
            echo "\n";
            $cpt++;
        }
        echo $nbCustomers . ' customers Processed!' . "\n";
        echo $nbCustomersOK . ' customers OK!' . "\n";
        echo $nbCustomersFirstNeighborhood . ' customers first neighborhood set! (multiple neighborhoods by website and no postcodes)' . "\n";
        echo count($customersNoNeighborhoods) . ' customers with no neighborhood' . "\n";
        echo "\n";
        foreach ($customersNoNeighborhoods as $customer) {
            echo '#' . $customer->getId() . ' / ' . $customer->getEmail() . "\n";
        }
        echo "\n";
        echo 'FINI !!';
    }

    /**
     * getNeighborhoodsByWebsiteId 
     * 
     * @param int $websiteId websiteId 
     * 
     * @return Apdc_Neighborhood_Model_Resource_Neighborhood_Collection
     */
    protected function getNeighborhoodsByWebsiteId($websiteId)
    {
        if (!isset($this->neighborhoodsByWebsiteIds[$websiteId])) {
            $this->neighborhoodsByWebsiteIds[$websiteId] = Mage::helper('apdc_neighborhood')->getNeighborhoodsByWebsiteId($websiteId);
        }

        return $this->neighborhoodsByWebsiteIds[$websiteId];
    }

    /**
     * getNeighborhoodByPostcode 
     * 
     * @param mixed $postcode postcode 
     * 
     * @return void
     */
    protected function getNeighborhoodByPostcode($postcode)
    {
        if (!isset($this->neighborhoodByPostcode[$postcode])) {
            $neighborhood =  Mage::helper('apdc_neighborhood')->getNeighborhoodByPostcode($postcode);
            if (is_null($neighborhood)) {
                return null;
            }
            $this->neighborhoodByPostcode[$postcode] = $neighborhood;
        }
        return $this->neighborhoodByPostcode[$postcode];
    }

    /**
     * getCustomerPostcode 
     * 
     * @param Mage_Customer_Model_Customer $customer customer 
     * 
     * @return string|null
     */
    protected function getCustomerPostcode(Mage_Customer_Model_Customer $customer)
    {
        $address = null;
        if ($customer->getDefaultShippingAddress()) {
            $address = $customer->getDefaultShippingAddress();
        } else if ($customer->getDefaultBillingAddress()) {
            $address = $customer->getDefaultBillingAddress();
        } else {
            $addresses = Mage::getModel('customer/entity_address_collection')
                ->setCustomerFilter($customer);
            $addresses->getSelect()->order('entity_id DESC');
            if ($addresses->count()) {
                $address = $addresses->getFirstItem();
            }
        }
        if ($address) {
            $address = $address->load($address->getId());
            return $address->getPostcode();
        }
        return null;
    }



    // Usage instructions
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f shell/Apdc/Neighborhood/associateCustomer.php [options]
 
  help                   This help
 
USAGE;
    }
}

// Instantiate
$shell = new Apdc_Neighborhood_Shell_AssociateCustomer();
 
// Initiate script
$shell->run();
