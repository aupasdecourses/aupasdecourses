<?php

namespace Apdc\ApdcBundle\Services;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Pdfbilling
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

    private function _printBillingSummaryHeader()
    {
        $increment_id = $this->_data['increment_id'];
        $data_s = $this->_data['summary'][0];
        $date_finalized = date('d/m/Y', strtotime($data_s['date_finalized']));
        $month = ucfirst(strftime('%B %G', strtotime(str_replace('/', '-', $data_s['billing_month']))));

        $standart_lh = $this->_lineHeight;
        $this->_lineHeight = 20;

        $this->_drawLogo($this->_currentpage, $this->_logo_h, $this->_logo_w);

        $this->_currentpage->drawText('Facture n° '.$increment_id, $this->_margin_horizontal, $this->_offset = $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText("Date d'émission: ".$date_finalized, $this->_margin_horizontal, $this->_offset = $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText("Magasin: {$data_s['shop']}", $this->_margin_horizontal, $this->_offset = $this->_offset - $this->_lineHeight);

        $this->_offset -= $this->_lineHeight * 7;

        $this->_currentpage->setFont($this->_font_bold, 12);

        $this->_currentpage->drawText("Facture de {$month} pour la prestation d'Au Pas De Courses", $this->_margin_horizontal, $this->_offset = $this->_offset - $this->_lineHeight);

        $this->_offset -= $this->_lineHeight * 3;
        $this->_lineHeight = $standart_lh;
    }

    private function _printBillingDetailsTemplate($start, $end)
    {
        $increment_id = $this->_data['increment_id'];
        $data = $this->_data['summary'][0];
        $date_finalized = $data['date_finalized'];
        $month = ucfirst(strftime('%B %G', strtotime(str_replace('/', '-', $data['billing_month']))));
        $id = $start;
        while ($id <= $end) {
            $this->_page[$id]->drawText("Au Pas De Courses - Facture n° {$increment_id} pour {$data['shop']} pour ".$month, $this->_margin_horizontal, $this->_height - $this->_margin_vertical / 1.5);
            $this->_page[$id]->setLineWidth(0.5);
            $this->_page[$id]->drawLine($this->_margin_horizontal, $this->_height - $this->_margin_vertical, $this->_width - $this->_margin_horizontal, $this->_height - $this->_margin_vertical);

            $this->_page[$id]->drawLine($this->_margin_horizontal, $this->_margin_vertical, $this->_width - $this->_margin_horizontal, $this->_margin_vertical);
            $this->_page[$id]->setFont($this->_font, 8);
            $this->_page[$id]->drawText('Généré le: '.$date_finalized, $this->_margin_horizontal, $this->_margin_vertical / 2);
            $id++;
        }
    }

    private function _printBillingPayout()
    {
        $this->_drawLogo($this->_currentpage, $this->_logo_h, $this->_logo_w,'BOTTOM_CENTER');

        $this->_offset = $this->_height-2*$this->_margin_vertical;
        $this->_currentpage->setFont($this->_font_bold, $this->_currentfontsize);

        $data=$this->_data["summary"][0];
        $month = ucfirst(strftime('%B %G', strtotime(str_replace('/', '-', $data['billing_month']))));

        $this->_currentpage->drawText("Calcul des sommes versées sur le compte de {$data['shop']},", $this->_margin_horizontal, $this->_offset = $this->_offset - $this->_lineHeight);
         $this->_currentpage->drawText("pour le mois de {$month}.", $this->_margin_horizontal, $this->_offset = $this->_offset - $this->_lineHeight);

        $this->_currentpage->setFont($this->_font, $this->_currentfontsize);
        $this->_offset -= $this->_lineHeight * 2;

        $this->_currentpage->drawText('Somme Ticket:', $this->_margin_horizontal, $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText($data['sum_ticket'], $this->_margin_horizontal+200, $this->_offset = $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText('Commission APDC:', $this->_margin_horizontal, $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText(-$data['sum_commission'], $this->_margin_horizontal+200, $this->_offset = $this->_offset - $this->_lineHeight);
        $this->_currentpage->setLineWidth(0.5);
        $this->_offset = $this->_offset - $this->_lineHeight;
        $this->_currentpage->drawLine($this->_margin_horizontal, $this->_offset, $this->_margin_horizontal+250, $this->_offset);
        $this->_currentpage->drawText('Somme due:', $this->_margin_horizontal, $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText($data['sum_due'], $this->_margin_horizontal+200, $this->_offset = $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText('Remise commerciale', $this->_margin_horizontal, $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText($data['discount_shop'], $this->_margin_horizontal+200, $this->_offset = $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText('Frais Hipay:', $this->_margin_horizontal, $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText(-$data['processing_fees'], $this->_margin_horizontal+200, $this->_offset = $this->_offset - $this->_lineHeight);

        $this->_currentpage->setFont($this->_font_bold, $this->_currentfontsize);
        $this->_currentpage->setLineWidth(0.5);
        $this->_offset = $this->_offset - $this->_lineHeight;
        $this->_currentpage->drawLine($this->_margin_horizontal, $this->_offset, $this->_margin_horizontal+250, $this->_offset);
        $this->_currentpage->drawText('Total versé:  ', $this->_margin_horizontal, $this->_offset - $this->_lineHeight);
        $this->_currentpage->drawText($data['sum_payout'], $this->_margin_horizontal+200, $this->_offset = $this->_offset - $this->_lineHeight);

        $this->_currentpage->setFont($this->_font, $this->_currentfontsize);
        $this->_offset -= $this->_lineHeight * 3;

        $this->_currentpage->drawText('Toutes les sommes sont en euros TTC', $this->_margin_horizontal, $this->_offset = $this->_offset - $this->_lineHeight);
    }

    public function printBillingShop($data)
    {
        $this->_data = $data;
        $data_s = $this->_data['summary'][0];
        $data_d = $this->_data['details'];

        //Table
        $table_s = [
            'config' => [
                'font_size_header' => 12,
                'font_size_body' => 12,
                'font_size_total' => 12,
                'fill_color_header' => '#188071',
                'line_color_header' => '#188071',
            ],
            'column_set' => [0.46, 0.135, 0.135, 0.135, 0.135],
            'header_type' => ['string', 'float', 'percent', 'float', 'float'],
            'header' => ['Prestation', 'Prix HT', '%TVA', 'TVA', 'Prix TTC'],
            'rows' => [
                ['Commission sur les ventes', $data_s['sum_commission_HT'], $data_s['sum_commission_TVA_percent'], $data_s['sum_commission_TVA'], $data_s['sum_commission']],
                //['Frais Bancaires', $data_s['processing_fees_HT'], $data_s['processing_fees_TVA_percent'], $data_s['processing_fees_TVA'], $data_s['processing_fees']],
                ],
            'total' => ['TOTAL', $data_s['sum_billing_HT'], '', $data_s['sum_billing_TVA'], $data_s['sum_billing']],
        ];

        if($data_s['discount_shop_HT']!=0){
            array_push($table_s['rows'],['Remise Commerciale', -$data_s['discount_shop_HT'], $data_s['discount_shop_TVA_percent'], -$data_s['discount_shop_TVA'], -$data_s['discount_shop']]);
        }

        //Create Billing Summary
        $this->_setPortraitTemplate();
        $this->_currentpage = $this->_page[0] = $this->_pdf->newPage($this->_format);
        $this->_currentpage->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $this->_currentpage->setFont($this->_font, $this->_currentfontsize);

        $this->_printBillingSummaryHeader();
        $this->_table_column_set = $this->_computeColumnSet($table_s['column_set']);
        $this->_printTable($table_s);

        $this->_offset -= $this->_lineHeight * 4;

        $this->_currentpage->drawText('Tous les prix sont en euros', $this->_margin_horizontal, $this->_offset = $this->_offset - $this->_lineHeight);

        $footer_text = 'Au Pas De Courses - SARL au capital de 12000€ - 31 rue de Constantinople 75008 Paris - RCS Paris 810 707 000 - Numéro de TVA Intracommunautaire - FR48810707000';

        $this->_printFooter($footer_text, 8);

        // create billing details pages
        $this->_setLandscapeTemplate();
        $this->_currentpage = $this->_page[1] = $this->_pdf->newPage($this->_format);
        $this->_currentpage->setFont($this->_font, 8);
        $this->_offset = $this->_height - 2 * $this->_margin_vertical;

        //Table
        $table_d = [
            'config' => [
                'font_size_header' => 8,
                'font_size_body' => 8,
                'font_size_total' => 8,
                'fill_color_header' => '#188071',
                'line_color_header' => '#188071',
            ],
            'column_set' => [0.07, 0.075, 0.07, 0.08, 0.13, 0.08, 0.085, 0.055, 0.06, 0.055, 0.06, 0.09, 0.09],
            'header_type' => ['string', 'string', 'string', 'string', 'string', 'float', 'float', 'float', 'float', 'float', 'float', 'float', 'float'],
            'header' => ['Date création', 'Date livraison', 'Mois factu', '#Commande', 'Client', 'Commande HT', 'Commande TTC', 'Avoir HT', 'Avoir TTC', 'Ticket HT', 'Ticket TTC', 'Commission HT', 'Somme due HT'],
            'rows' => [],
            'total' => ['TOTAL', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0],
        ];

        foreach ($data_d as $id => $row) {
            unset($row['order_shop_id']);
            unset($row['id_billing']);
            unset($row['id']);
            unset($row['shop_id']);
            unset($row['shop']);
            $data_d[$id] = $row;
        }

        $table_d['rows'] = $data_d;

        $this->_table_column_set = $this->_computeColumnSet($table_d['column_set']);
        $table_d = $this->_computeTotals($table_d);
        $this->_printTable($table_d);
        $this->_printBillingDetailsTemplate(1, count($this->_page) - 1);

        // create billing payout page
        $this->_setLandscapeTemplate();
        $this->_currentpage = $this->_page[count($this->_page)] = $this->_pdf->newPage($this->_format);
        $this->_currentpage->setFont($this->_font, 8);
        $this->_printBillingDetailsTemplate(count($this->_page)-1, count($this->_page) - 1);
        $this->_offset = $this->_height - 2 * $this->_margin_vertical;
        $this->_printBillingPayout();
        $this->_pageCount(1);

    }
}
