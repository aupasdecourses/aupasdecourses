<?php

namespace Apdc\ApdcBundle\Services;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Pdforder
{
    use Pdf;

    const FLOAT_NUMBER = 2;
    private $_router;
    private $_rootdir;

    //Pdf
    private $_pdf;
    private $_finalized = false;

    //Locale
    private $_currency;

    //Font
    private $_font;
    private $_font_bold;
    private $_font_italic;
    private $_currentfontsize = 12;

    //Format page
    private static $_width_ls = 842;
    private static $_height_ls = 595;
    private static $_width_po = 595;
    private static $_height_po = 842;
    private $_height;
    private $_width;
    private $_format;
    private $_page = [];

    //Table format
    private $_table_column_set;
    private $_table_padding = 5;

    //Margin & Offset Page
    private $_margin_horizontal = 20;
    private $_margin_vertical = 20;
    private $_lineHeight = 15;
    private $_page_columnWidth;
    private $_page_columnOffset;
    private $_page_maxLineOffset;
    private $_page_lineOffset;
    private $_ls_startColumnOffset = 0;
    private $_ls_maxColumnOffset = 2;
    private $_ls_startLineOffset = 9;
    private $_offset;

    //Ressources
    private $_logo;
    private $_logo_h = 100;
    private $_logo_w = 175;

    //Order summary template
    private $_summary_lineHeight = 20;
    private $_summary = [];
    private $_summary_id = 0;

    //Order lists template
    private $_orders_startLineOffset_first = 14;
    private $_orders_startLineOffset_second = 8;
    private static $_orders_table_column_set = [5, 120, 185, 300, 415, 475];
    private $_orders_template;
    private $_orders = [];
    private $_orders_count = 0;
    private $_date;
    private $_name;
    private $_mails = [];

    private $_currentpage;
    private $_data;

    public function __construct($rootdir, $router)
    {
        //To use controller methods
        $this->_router = $router;
        $this->_rootdir = $rootdir.'/../..';

        $this->_pdf = new \Zend_Pdf();

        $this->_font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);
        $this->_font_bold = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $this->_font_italic = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA_ITALIC);
        $this->_font_bold_italic = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA_BOLD_ITALIC);

        $this->_logo = $this->_rootdir.$this->generateUrl('root').'img/logo_pdf.png';

        setlocale(LC_TIME, 'fr_FR.UTF8');
        setlocale(LC_CTYPE, 'fr_FR.UTF8');
        $this->_locale_info = localeconv();
    }

    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->_router->generate($route, $parameters, $referenceType);
    }

    public function setOrderTemplate()
    {
        $this->_setLandscapeTemplate();

        // create summary first page and draw header ==>>
        $this->_summary[0] = $this->_pdf->newPage($this->_format);
        $this->_summary[0]->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $this->_summary[0]->setFont($this->_font, $this->_currentfontsize);
        $this->_summary[0]->drawText('Commandes AU PAS DE COURSES', $this->_margin_horizontal, $this->_height - ($this->_summary_lineHeight * 5));
        $this->_summary[0]->setFont($this->_font, $this->_currentfontsize);
        $this->_summary[0]->drawText("A {$this->_name} pour le {$this->_date}", $this->_margin_horizontal, $this->_height - ($this->_summary_lineHeight * 6));

        //$this->_summary[0]->drawImage($image, $this->_width - $this->_margin_horizontal - $image->getPixelWidth(), $this->_height - $image->getPixelHeight() - ($this->_lineHeight * 6), $this->_width - $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 6));
        // <<==

        // create orders template page ==>>
        $this->_orders_template = $this->_pdf->newPage($this->_format);

        $this->_orders_template->setFont($this->_font, 8);
        $this->_orders_template->drawText("Commande Au Pas De Courses - {$this->_name} pour le {$this->_date}", $this->_margin_horizontal, $this->_height - $this->_margin_vertical + 10);
        $this->_orders_template->setLineWidth(0.5);
        $this->_orders_template->drawLine($this->_margin_horizontal, $this->_height - $this->_margin_vertical, $this->_width - $this->_margin_horizontal, $this->_height - $this->_margin_vertical);

        $this->_orders_template->drawLine($this->_margin_horizontal, $this->_margin_vertical, $this->_width - $this->_margin_horizontal, $this->_margin_vertical);
        $this->_orders_template->setFont($this->_font, 8);
        $this->_orders_template->drawText('Généré le: '.date('r'), $this->_margin_horizontal, $this->_margin_vertical - 10);
        // <<==
    }

    public function setDate($date)
    {
        $this->_date = $date;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function setMails(array $mails)
    {
        $this->_mails = $mails;
    }

    private function _finalizePdf()
    {
        $page_id = 1;
        //$page_count = count($this->_summary) + count($this->_orders);
        $page_count = count($this->_page);
        //$this->_summary[0]->drawText('Nombre de commandes: '.$this->_orders_count, $this->_margin_horizontal, $this->_height - ($this->_summary_lineHeight * 8));
        foreach ($this->_page as $page) {
            //$page->setFont($this->_font, 8);
            //$page->drawText("page {$page_id}/{$page_count}", $this->_width - ($this->_margin_horizontal * 2), $this->_margin_vertical - 10);
            //++$page_id;
            $this->_pdf->pages[] = $page;
        }
        /*foreach ($this->_orders as $page) {
            $page->setFont($this->_font, 8);
            $page->drawText("page {$page_id}/{$page_count}", $this->_width - ($this->_margin_horizontal * 2), $this->_margin_vertical - 10);
            ++$page_id;
            $this->_pdf->pages[] = $page;
        }*/
    }

    private function addOrderToSummary($order)
    {
        if (!($this->_page_lineOffset < $this->_page_maxLineOffset)) { // add column to current summary
            if ($this->_page_columnOffset < $this->_ls_maxColumnOffset) {
                ++$this->_page_columnOffset;
            } else { // add page to summary
                $this->_ls_startLineOffset = 5;
                $this->_page_columnOffset = $this->_ls_startColumnOffset;
                ++$this->_summary_id;
                $this->_summary[$this->_summary_id] = $this->_pdf->newPage($this->_format);
                $this->_summary[$this->_summary_id]->setFont($this->_font, 12);
            }
            // reinitialyse line offset to start position
            $this->_page_lineOffset = $this->_ls_startLineOffset;
        }
        // add current order to summary
        $this->_summary[$this->_summary_id]->drawText('Commande n°'.++$this->_orders_count.": {$order['id']}", $this->_margin_horizontal + ($this->_page_columnWidth * $this->_page_columnOffset), $this->_height - ($this->_summary_lineHeight * $this->_page_lineOffset++));
    }

    private function _orders_header_draw(&$page)
    {
        $page->setFont($this->_font, 12);
        $page->setFillColor(new \Zend_Pdf_Color_Html('#188071'));
        $page->setLineColor(new \Zend_Pdf_Color_Html('#188071'));
        $page->drawRectangle($this->_margin_horizontal, $this->_height - ($this->_lineHeight * $this->_page_lineOffset), $this->_width - $this->_margin_horizontal, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset - 2)));
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
        $page->drawText('Produit', $this->_margin_horizontal + static::$_orders_table_column_set[0], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset - 0.75)));
        $page->drawText('Quantité', $this->_margin_horizontal + static::$_orders_table_column_set[1], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset - 0.75)));
        $page->drawText('Description', $this->_margin_horizontal + static::$_orders_table_column_set[2], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset - 0.75)));
        $page->drawText('Prix', $this->_margin_horizontal + static::$_orders_table_column_set[3], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset - 0.75)));
        $page->drawText('Total', $this->_margin_horizontal + static::$_orders_table_column_set[4], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset - 0.75)));
        $page->drawText('Commentaire Client', $this->_margin_horizontal + static::$_orders_table_column_set[5], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset - 0.75)));
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
    }

    private function _textPrint($text, &$page, $column_begin)
    {
        $line_id = 0;

        foreach ($text as $line) {
            $page->drawText($line, $this->_margin_horizontal + $column_begin, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + $line_id + 0.75)));
            ++$line_id;
        }
    }

    private function _orders_row_draw($product, &$page, $id)
    {
        if ($id % 2) {
            $color = new \Zend_Pdf_Color_Rgb(1, 1, 1);
        } else {
            $color = new \Zend_Pdf_Color_Html('#F0F0F0');
        }
        $column_line_color = new \Zend_Pdf_Color_Html('#D3D3D3');

        $title = static::_lineSplit($product['title'], 21);
        $description = static::_lineSplit($product['description'], 20);
        $comment = static::_lineSplit($product['comment'], 50);

        $max_height = max([1, count($title), count($quantite), count($comment)]);
        $page->setFillColor($color);
        $page->setLineColor($color);
        $page->drawRectangle($this->_margin_horizontal, $this->_height - ($this->_lineHeight * $this->_page_lineOffset), $this->_width - $this->_margin_horizontal, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + $max_height)));
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $page->setLineColor($column_line_color);

        $this->_textPrint($title, $page, static::$_orders_table_column_set[0]);

        $page->drawLine($this->_margin_horizontal + static::$_orders_table_column_set[1] - 2, $this->_height - ($this->_lineHeight * $this->_page_lineOffset), $this->_margin_horizontal + static::$_orders_table_column_set[1] - 2, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + $max_height)));
        $page->setFont($this->_font_bold, 10);
        if ($product['quantite'] > 1) {
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 0, 0));
        }
        $page->drawText("{$product['quantite']} x", $this->_margin_horizontal + static::$_orders_table_column_set[1] + 15, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + 0.75)));
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $page->setFont($this->_font, 10);

        $page->drawLine($this->_margin_horizontal + static::$_orders_table_column_set[2] - 5, $this->_height - ($this->_lineHeight * $this->_page_lineOffset), $this->_margin_horizontal + static::$_orders_table_column_set[2] - 5, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + $max_height)));
        $this->_textPrint($description, $page, static::$_orders_table_column_set[2]);

        $page->drawLine($this->_margin_horizontal + static::$_orders_table_column_set[3] - 5, $this->_height - ($this->_lineHeight * $this->_page_lineOffset), $this->_margin_horizontal + static::$_orders_table_column_set[3] - 5, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + $max_height)));
        $page->drawText($product['prix_unitaire']."€ ({$product['prix_kilo']})", $this->_margin_horizontal + static::$_orders_table_column_set[3], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + 0.75)));

        $page->drawLine($this->_margin_horizontal + static::$_orders_table_column_set[4] - 5, $this->_height - ($this->_lineHeight * $this->_page_lineOffset), $this->_margin_horizontal + static::$_orders_table_column_set[4] - 5, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + $max_height)));
        $page->drawText("{$product['prix_total']}€", $this->_margin_horizontal + static::$_orders_table_column_set[4], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + 0.75)));

        $page->drawLine($this->_margin_horizontal + static::$_orders_table_column_set[5] - 5, $this->_height - ($this->_lineHeight * $this->_page_lineOffset), $this->_margin_horizontal + static::$_orders_table_column_set[5] - 5, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + $max_height)));
        $page->setFont($this->_font_bold, 10);
        $this->_textPrint($comment, $page, static::$_orders_table_column_set[5]);
        $page->setFont($this->_font, 10);

        $this->_page_lineOffset += $max_height;
    }

    private function _orders_table_draw($order, &$pages)
    {
        $page_id = 0;
        $id = 0;

        foreach ($order['products'] as $product) {
            if (!($this->_page_lineOffset < $this->_page_maxLineOffset)) { // add new page to order
                $id = 0;
                ++$page_id;
                $pages[$page_id] = clone $this->_orders_template;
                $this->_page_lineOffset = $this->_orders_startLineOffset_second;
                $this->_orders_header_draw($pages[$page_id]);
                $pages[$page_id]->setFont($this->_font, $this->_currentfontsize);
                $pages[$page_id]->drawText("Commande n°  {$order['id']}", $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 5));
                $pages[$page_id]->setFont($this->_font, 10);
            }
            $this->_orders_row_draw($product, $pages[$page_id], $id);
            ++$id;
        }
    }

    private function _orders_total_draw($order, &$page)
    {
        $page->setFont($this->_font_bold, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Html('#188071'));
        $page->setLineColor(new \Zend_Pdf_Color_Html('#188071'));
        $page->drawRectangle($this->_margin_horizontal, $this->_height - ($this->_lineHeight * $this->_page_lineOffset), $this->_width - $this->_margin_horizontal, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + 2)));
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
        $page->drawText('TOTAL', $this->_margin_horizontal + static::$_orders_table_column_set[0] + 150, $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + 1.25)));
        $page->drawText("{$order['Total prix']}€", $this->_margin_horizontal + static::$_orders_table_column_set[4], $this->_height - ($this->_lineHeight * ($this->_page_lineOffset + 1.25)));
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
    }

    public function getOrdersCount()
    {
        return $this->_orders_count;
    }

    public function addOrder($order)
    {
        $pages = [];

        $this->addOrderToSummary($order);
        $pages[0] = clone $this->_orders_template;

        $pages[0]->setFont($this->_font, $this->_currentfontsize);
        $pages[0]->drawText("Commande n°  {$order['id']}", $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 5));
        $pages[0]->setFont($this->_font, 12);
        $pages[0]->drawText("Nom du client: {$order['first_name']} {$order['last_name']}", $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 6));
        $pages[0]->drawText("Date de Prise de Commande: {$order['order_date']}", $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 7));
        $pages[0]->drawText("Date de Livraison: {$order['delivery_date']}", $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 8));
//        $pages[0]->drawText("Creneau de Livraison: {$order['delivery_time']}", $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 9));
        $pages[0]->drawText('Remplacement equivalent: ', $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 10));    // <===
        if ($order['equivalent_replacement']) {
            $pages[0]->drawText('oui', $this->_margin_horizontal + static::$_orders_table_column_set[1] + 50, $this->_height - ($this->_lineHeight * 10));
        } else {
            $pages[0]->setFont($this->_font_bold, 12);
            $pages[0]->setFillColor(new \Zend_Pdf_Color_Rgb(1, 0, 0));
            $pages[0]->drawText('non', $this->_margin_horizontal + static::$_orders_table_column_set[1] + 50, $this->_height - ($this->_lineHeight * 10));
            $pages[0]->setFont($this->_font, 12);
            $pages[0]->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        }
        $pages[0]->drawText('Liste des produits commandes: ', $this->_margin_horizontal, $this->_height - ($this->_lineHeight * 11));

        $this->_page_lineOffset = $this->_orders_startLineOffset_first;

        $this->_orders_header_draw($pages[0]);
        $pages[0]->setFont($this->_font, 10);
        $this->_orders_table_draw($order, $pages);
        $this->_orders_total_draw($order, $pages[count($pages) - 1]);

        $page_count = count($pages);
        $page_id = 1;
        foreach ($pages as $page) { // finalyse order
            $page->setFont($this->_font, $this->_currentfontsize);
            $page->drawText("{$page_id}/{$page_count}", $this->_width - $this->_margin_horizontal - 50, $this->_height - ($this->_lineHeight * 5));
            $this->_orders[] = $page;
            ++$page_id;
        }
    }

    public function save($filename)
    {
        if (!$this->_finalized) {
            $this->_finalizePdf();
        }
        $this->_pdf->save($filename);
    }

    public function send($smtp_host = 'smtp.mandrillapp.com', $smtp_config = ['auth' => 'login', 'username' => 'pierre@aupasdecourses.com', 'password' => 'suQMuVOzZHE5kc-wmH3oUA', 'port' => 2525, 'return-path' => 'contact@aupasdecourses.com'])
    {
        if (!$this->_finalized) {
            $this->_finalizePdf();
        }
        $pdf = $this->_pdf->render();
        $tr = new \Zend_Mail_Transport_Smtp($smtp_host, $smtp_config);
        $mail = new \Zend_Mail('utf-8');
        $tmp = [];
        foreach ($this->_mails as $m) {
            if ($m != '' && $m != '') {
                $tmp[] = $m;
            }
        }
        //test $tmp="pierre@aupasdecourses.com";
        $mail->addTo($tmp);
        $mail->addCc(\Mage::getStoreConfig('trans_email/ident_general/email'));
        $mail->setFrom(\Mage::getStoreConfig('trans_email/ident_general/email'), "L'équipe d'Au Pas De Courses");
        $mail->setSubject("Au Pas De Courses {$this->_orders_count} commandes le {$this->_date}");
        $mail->setBodyHtml(
            \Mage::getModel('core/email_template')->loadByCode('APDC::Mail envoi commande commerçants')
            ->getProcessedTemplate(['commercant' => $this->_name, 'nbecommande' => $this->_orders_count])
        );
        $attach = new \Zend_Mime_Part($pdf);
        $attach->type = 'application/pdf';
        $attach->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
        $attach->encoding = \Zend_Mime::ENCODING_BASE64;
        $attach->filename = "{$this->_name}_{$this->_date}.pdf";
        $mail->addAttachment($attach);
        try {
            $mail->send($tr);
        } catch (Exception $e) {
            \Mage::log($e, null, 'send_daily_order.log');
        }
    }
}
