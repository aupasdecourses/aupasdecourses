<?php
/**
 * Inchoo PDF rewrite for custom attribute
 * * Attribute "inchoo_warehouse_location" has to be set manually
 * Original: Sales Order Invoice PDF model
 *
 * @category   Inchoo
 * @package    Inhoo_Invoice
 * @author     Mladen Lotar - Inchoo <mladen.lotar@inchoo.net>
 */
class Pmainguet_Invoice_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    /**
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $object->setFont($font, $size);
        return $font;
    }
	
	/**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_ITALIC);
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Insert logo to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 830; //top border of the page
                $widthLimit  = 100; //half of the page width
                $heightLimit = 100; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 25;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }

    /**
     * Insert address to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(100));
        $font = $this->_setFontRegular($page, 10);
        $page->setLineWidth(0);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
        foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $value){
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(trim(strip_tags($_value)),
                        $this->getAlignRight($_value, 130, 440, $font, 10),
                        $top,
                        'UTF-8');
                    $top -= 10;
                }
            }
        }
        $this->y = ($this->y > $top) ? $top : $this->y;
    }

    /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Commerçant'),
            'feed' => 35
        );

        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 125
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 365,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Price'),
            'feed'  => 415,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 455,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Tax'),
            'feed'  => 495,
            'align' => 'right'
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Total'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Insert order to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $obj
     * @param bool $putOrderId
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
        $this->_setFontBold($page, 12);
        $page->drawText('Liste des produits commandés', 35, $top-15, 'UTF-8');
        $this->y-=30;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
    }

    /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function drawtableMWDdate(Zend_Pdf_Page $page, $ddate = array())
    {
        $label='Date de livraison';
        $page->drawText($label . ': ', 45, $this->y, 'UTF-8');
        $page->drawText($ddate['ddate'], 150, $this->y, 'UTF-8');
        $this->y -= 15;
     
        $label='Horaire de livraison';
        $page->drawText($label . ': ', 45, $this->y, 'UTF-8');
        $page->drawText($ddate['dtimetext'], 150, $this->y, 'UTF-8');
        $this->y -= 15;

    }

     /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            
            $order = $invoice->getOrder();

            //PAGE DE FACTURATION DE LA LIVRAISON
            $page  = $this->newPage();
			/* Add image */
            $this->insertLogo($page, $invoice->getStore());

           //Draw document info
            $this->y = $this->y ? $this->y : 815;
	        $top = $this->y;
	        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
	        $this->_setFontItalic($page, 11);

	        //Invoice number
	        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
	        $docHeader = $this->getDocHeaderCoordinates();
	        $page->drawText(Mage::helper('sales')->__('Facture n° ') . $invoice->getIncrementId(), 35, $docHeader[1]-15, 'UTF-8');

	        //Order number
            $page->drawText(Mage::helper('sales')->__('Commande n° ') . $order->getRealOrderId(), 350, $docHeader[1]-15, 'UTF-8');

	        //Date émission de la facture
	        $page->drawText('Date d\'émission: '. Mage::helper('core')->formatDate($invoice->getCreatedAt(),'medium',false), 35, $top-30, 'UTF-8');

	        //Order date
	        $page->drawText(
	            Mage::helper('sales')->__('Date de la commande: ') . Mage::helper('core')->formatDate(
	                $order->getCreatedAtStoreDate(), 'medium', false
	            ),
	            350,
	            $top-30,
	            'UTF-8'
	        );

	        //Infos clients
	        $top -= 60;

	        //Get and format addresses
	        $billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
	        if (!$order->getIsVirtual()) {
	            $shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
	            $shippingMethod  = $order->getShippingDescription();
	        }

	        //Draw addresses titles
	        $this->_setFontBold($page, 12);
	        $page->drawText(Mage::helper('sales')->__('Adresse de facturation:'), 55, ($top - 15), 'UTF-8');
	        if (!$order->getIsVirtual()) {
	            $page->drawText(Mage::helper('sales')->__('Adresse de livraison:'), 330, ($top - 15), 'UTF-8');
	        } else {
	            $page->drawText(Mage::helper('sales')->__('Méthode de paiement:'), 330, ($top - 15), 'UTF-8');
	        }

	        //Draw billing address
	        $addressesHeight = $this->_calcAddressHeight($billingAddress);
	        if (isset($shippingAddress)) {
	            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
	        }

	        $this->_setFontRegular($page, 10);
	        $this->y = $top - 40;
	        $addressesStartY = $this->y;

	        foreach ($billingAddress as $value){
	            if ($value !== '') {
	                $text = array();
	                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
	                    $text[] = $_value;
	                }
	                foreach ($text as $part) {
	                    $page->drawText(strip_tags(ltrim($part)), 65, $this->y, 'UTF-8');
	                    $this->y -= 15;
	                }
	            }
	        }

	        $addressesEndY = $this->y;

	       	//Draw shipping address
	        if (!$order->getIsVirtual()) {
	            $this->y = $addressesStartY;
	            foreach ($shippingAddress as $value){
	                if ($value!=='') {
	                    $text = array();
	                    foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
	                        $text[] = $_value;
	                    }
	                    foreach ($text as $part) {
	                        $page->drawText(strip_tags(ltrim($part)), 340, $this->y, 'UTF-8');
	                        $this->y -= 15;
	                    }
	                }
	            }

	            $addressesEndY = min($addressesEndY, $this->y);
	            $this->y = $addressesEndY;

	            $this->y -= 15;


	            //Draw method titles
	            $this->_setFontBold($page, 12);
	            $page->drawText(Mage::helper('sales')->__('Méthode de paiement'), 55, $this->y, 'UTF-8');
	            $this->y -=10;
	            $this->_setFontRegular($page, 10);
	            $paymentLeft = 65;
	            $yPayments   = $this->y - 15;
	        }
	        else {
	            $yPayments   = $addressesStartY;
	            $paymentLeft = 285;
	        }


	        //Draw payment info
	        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
	            ->setIsSecureMode(true)
	            ->toPdf();
	        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
	        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
	        foreach ($payment as $key=>$value){
	            if (strip_tags(trim($value)) == '') {
	                unset($payment[$key]);
	            }
	        }
	        reset($payment);

	        foreach ($payment as $value){
	            if (trim($value) != '') {
	                //Printing "Payment Method" lines
	                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
	                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
	                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
	                    $yPayments -= 15;
	                }
	            }
	        }

	        //Draw shipping method line
            $this->y-= 60;
	        $this->_setFontBold($page, 12);
	        $page->drawText(Mage::helper('sales')->__('Description de la prestation d\'Au Pas De Courses'), 55, $this->y, 'UTF-8');
	        $this->y-= 30;

            //Draw headers
          	$this->_setFontBold($page, 10);
          	$init=60;
          	$page->drawText('Mode de livraison', $init, $this->y, 'UTF-8');
            $page->drawText('Prix HT', $init+300, $this->y, 'UTF-8');
            $page->drawText('% TVA', $init+355, $this->y, 'UTF-8');
            $page->drawText('TVA', $init+410, $this->y, 'UTF-8');
            $page->drawText('Prix TTC', $init+460, $this->y, 'UTF-8');

            
            //Draw shipping method description and price line
            $this->y-= 20;
            $shippingline=$this->y;
          	$this->_setFontRegular($page, 10);   
            $totalShippingChargesHT = $order->formatPriceTxt($order->getShippingAmount());
            $totalShippingChargesTax = $order->formatPriceTxt($order->getShippingTaxAmount());
            $totalShippingChargesTTC = $order->formatPriceTxt($order->getShippingInclTax());

            foreach (Mage::helper('core/string')->str_split($shippingMethod, 50, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), $init, $this->y, 'UTF-8');
                $this->y -= 15;
            }   
            $page->drawText($totalShippingChargesHT, $init+300, $shippingline, 'UTF-8');
            $page->drawText("20%", $init+355, $shippingline, 'UTF-8');
            $page->drawText($totalShippingChargesTax, $init+410, $shippingline, 'UTF-8');
            $page->drawText($totalShippingChargesTTC, $init+460, $shippingline, 'UTF-8');

            //Footer
            $this->_setFontRegular($page, 7); 
            $page->drawText('Le montant de l\'indemnité forfaitaire pour frais de recouvrement due en cas de retard de paiement,', 30, 100, 'UTF-8');
            $page->drawText('s\'élève à 40 € conformément à l\'article 121-II de la loi n° 2012-387 du 22 mars 2012 et au décret n° 2012-1115 du 2 octobre 2012.', 30, 90, 'UTF-8');
            $page->drawText('Le client déclare avoir accepté les conditions générales de vente au moment de la validation de sa commande,', 30, 80, 'UTF-8');
            $page->drawText(', visibles sur le site '.Mage::getBaseUrl(), 30, 70, 'UTF-8');
            $this->_setFontItalic($page, 8); 
            $page->drawText(Mage::getStoreConfig('general/store_information/name').' - SAS au capital de 12000€ - '.Mage::getStoreConfig('general/store_information/address').' - RCS Paris '.Mage::getStoreConfig('general/store_information/numero_RCS'), 30, 50, 'UTF-8');
            $page->drawText('Numéro de TVA Intracommunautaire - '.Mage::getStoreConfig('general/store_information/TVA_intra'), 30, 40, 'UTF-8');

            //________PAGE D'INFORMATION AMASTY________//

            $page  = $this->newPage();
            $this->y = $this->y ? $this->y : 815;
            $top = $this->y;
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());

            /* Add Title*/
            $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
            $docHeader = $this->getDocHeaderCoordinates();
            $this->_setFontBold($page,12);
            $page->drawText(Mage::helper('sales')->__('Informations Livraison'), 35, $docHeader[1]-30, 'UTF-8');
            $this->y-=45;

            $this->_setFontItalic($page,11);
            $page->drawText('Informations sur le créneau de livraison', 35, $this->y, 'UTF-8');
            $this->y -= 20;
            $this->_setFontRegular($page,10);

            /* Remove `Amasty Delivery Date` */
            //if (Mage::helper('core')->isModuleEnabled('Amasty_Deliverydate')) {
                //Mage::helper('amdeliverydate/pdf')->addDeliverydate($page, $order, $this);
            //}

            $ddate = Mage::getResourceModel('ddate/ddate')->getDdateByOrder($invoice->getOrderIncrementId());
            $this->drawtableMWDdate($page, $ddate);

            $this->y -= 30;
            $this->_setFontItalic($page,11);
            $page->drawText('Informations sur le lieu de livraison et le client', 35, $this->y, 'UTF-8');
            $this->y -= 20;
            $this->_setFontRegular($page,10);

            /* Add `Amasty Order Attributes` */
            if (Mage::helper('core')->isModuleEnabled('Amasty_Orderattr')) {
                Mage::helper('amorderattr/pdf')->addAttrbutes($page, $order, $this);
            }

            //________PAGE DE RECU PRODUITS________//

            $page  = $this->newPage();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            // $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            // $this->insertDocumentNumber(
            //     $page,
            //     Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
            // );
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}

