<?php

namespace Apdc\ApdcBundle\Services;

trait Pdf
{
    // TEXT GENERAL FUNCTIONS

    private function _stringWidth($string, $fontsize)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); ++$i) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $this->_font->glyphNumbersForCharacters($characters);
        $widths = $this->_font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $this->_font->getUnitsPerEm()) * $fontsize;

        return $stringWidth;
    }

    private function _lineSplit($line, $fontsize, $width)
    {
        $rsl = [''];
        $line_id = 0;
        $split = preg_split("/[\s]+/", $line);

        foreach ($split as $word) {
            if ($this->_stringWidth($rsl[$line_id], $fontsize) + $this->_stringWidth(" $word", $fontsize) > $width) {
                ++$line_id;
                $rsl[$line_id] = '';
            }
            $rsl[$line_id] .= " $word";
        }

        return $rsl;
    }

    // PDF GENERAL FUNCTIONS

    /**
     * General function to set landscape template for a page.
     */
    private function _setLandscapeTemplate()
    {
        $this->_width = static::$_width_ls;
        $this->_height = static::$_height_ls;
        $this->_page_columnWidth = ($this->_width - ($this->_margin_horizontal * 2)) / number_format($this->_ls_maxColumnOffset + 1, 2);
        $this->_page_columnOffset = $this->_ls_startColumnOffset;
        $this->_page_lineOffset = $this->_ls_startLineOffset;
        $this->_page_maxLineOffset = ($this->_height / $this->_lineHeight) - ($this->_margin_vertical / $this->_lineHeight) - 3;

        $this->_format = $this->_width.':'.$this->_height.':';
    }

    /**
     * General function to set portrait landscape for a page.
     */
    private function _setPortraitTemplate()
    {
        $this->_width = static::$_width_po;
        $this->_height = static::$_height_po;
        $this->_page_columnWidth = ($this->_width - ($this->_margin_horizontal * 2)) / number_format($this->_ls_maxColumnOffset + 1, 2);
        $this->_page_columnOffset = $this->_ls_startColumnOffset;
        $this->_page_lineOffset = $this->_ls_startLineOffset;
        $this->_page_maxLineOffset = ($this->_height / $this->_lineHeight) - ($this->_margin_vertical / $this->_lineHeight) - 3;

        $this->_format = $this->_width.':'.$this->_height.':';
    }

    private function _drawLogo($page, $height, $width, $position = 'UPPER_LEFT')
    {
        $image = \Zend_Pdf_Image::imageWithPath($this->_logo);
        switch ($position) {
            case 'UPPER_LEFT':
                $page->drawImage($image, $this->_margin_horizontal, $this->_height - $height - $this->_margin_vertical, $width, ($this->_height - $this->_margin_vertical));
                break;
            case 'UPPER_CENTER':
                $page->drawImage($image, ($this->_width - $width) / 2, $this->_height - $height - $this->_margin_vertical, ($this->_width + $width) / 2, ($this->_height - $this->_margin_vertical));
                break;
            case 'UPPER_RIGHT':
                $page->drawImage($image, $this->_width - $this->_margin_horizontal - $width, $this->_height - $height - $this->_margin_vertical, $this->_width - $this->_margin_horizontal, ($this->_height - $this->_margin_vertical));
                break;
            case 'BOTTOM_LEFT':
                $page->drawImage($image, $this->_margin_horizontal, $this->_margin_vertical, $width, $this->_margin_vertical + $height);
                break;
            case 'BOTTOM_CENTER':
                $page->drawImage($image, ($this->_width - $width) / 2, $this->_margin_vertical, ($this->_width + $width) / 2, $this->_margin_vertical + $height);
                break;
            case 'BOTTOM_RIGHT':
                $page->drawImage($image, $this->_width - $this->_margin_horizontal - $width, $this->_margin_vertical, $this->_width - $this->_margin_horizontal, $this->_margin_vertical + $height);
                break;
        }
        $this->_offset = $this->_height - $height - $this->_margin_vertical;
    }

    private function _printFooter($text, $fontsize)
    {
        $this->_currentpage->setFont($this->_font, $fontsize);
        $text = $this->_lineSplit($text, $fontsize, $this->_width - 2 * $this->_margin_horizontal);
        foreach ($text as $id => $line) {
            $this->_currentpage->drawText($line, $this->_margin_horizontal, $this->_margin_vertical + $this->_lineHeight * (count($text) - ($id + 1)));
        }
        $this->_currentpage->setFont($this->_font, $this->_currentfontsize);
    }

    private function _pageCount($start_count)
    {
        $page_id = 1;
        $page_count = count($this->_page) - $start_count;
        foreach ($this->_page as $id => $page) {
            if ($id >= $start_count) {
                $page->setFont($this->_font, 8);
                $string = "page {$page_id}/{$page_count}";
                $page->drawText($string, $this->_width - $this->_margin_horizontal - $this->_stringWidth($string, 8), $this->_margin_vertical - 10);
                ++$page_id;
            }
        }
    }

    private function _finalizePdf()
    {
        //$page_id = 1;
        //$page_count = count($this->_summary) + count($this->_orders);
        //$page_count = count($this->_page);
        //$this->_summary[0]->drawText('Nombre de commandes: '.$this->_orders_count, $this->_margin_horizontal, $this->_height - ($this->_summary_lineHeight * 8));
        //$this->_pageCount(1);
        $this->_pdf->pages = $this->_page;
        /*foreach ($this->_orders as $page) {
            $page->setFont($this->_font, 8);
            $page->drawText("page {$page_id}/{$page_count}", $this->_width - ($this->_margin_horizontal * 2), $this->_margin_vertical - 10);
            ++$page_id;
            $this->_pdf->pages[] = $page;
        }*/
    }

    public function save($filename)
    {
        if (!$this->_finalized) {
            $this->_finalizePdf();
        }
        $this->_pdf->save($filename);
    }

    // public function send($smtp_host = 'smtp.mandrillapp.com', $smtp_config = ['auth' => 'login', 'username' => 'pierre@aupasdecourses.com', 'password' => 'suQMuVOzZHE5kc-wmH3oUA', 'port' => 2525, 'return-path' => 'contact@aupasdecourses.com'])
    // {
    //     if (!$this->_finalized) {
    //         $this->_finalizePdf();
    //     }
    //     $pdf = $this->_pdf->render();
    //     $tr = new \Zend_Mail_Transport_Smtp($smtp_host, $smtp_config);
    //     $mail = new \Zend_Mail('utf-8');
    //     $tmp = [];
    //     foreach ($this->_mails as $m) {
    //         if ($m != '' && $m != '') {
    //             $tmp[] = $m;
    //         }
    //     }
    //     //test $tmp="pierre@aupasdecourses.com";
    //     $mail->addTo($tmp);
    //     $mail->addCc(\Mage::getStoreConfig('trans_email/ident_general/email'));
    //     $mail->setFrom(\Mage::getStoreConfig('trans_email/ident_general/email'), "L'équipe d'Au Pas De Courses");
    //     $mail->setSubject("Au Pas De Courses {$this->_orders_count} commandes le {$this->_date}");
    //     $mail->setBodyHtml(
    //         \Mage::getModel('core/email_template')->loadByCode('APDC::Mail envoi commande commerçants')
    //         ->getProcessedTemplate(['commercant' => $this->_name, 'nbecommande' => $this->_orders_count])
    //     );
    //     $attach = new \Zend_Mime_Part($pdf);
    //     $attach->type = 'application/pdf';
    //     $attach->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
    //     $attach->encoding = \Zend_Mime::ENCODING_BASE64;
    //     $attach->filename = "{$this->_name}_{$this->_date}.pdf";
    //     $mail->addAttachment($attach);
    //     try {
    //         $mail->send($tr);
    //     } catch (Exception $e) {
    //         \Mage::log($e, null, 'send_daily_order.log');
    //     }
    // }

    public function send($data)
    {
        $templateId = $data['mail_template'];
        $sender = [
            'name' => "L'équipe d'Au Pas De Courses",
            'email' => \Mage::getStoreConfig('trans_email/ident_general/email')
        ];

        $nameTo = 'truc';
        $emailTo = $data['mails'];
        $vars = $data['mail_vars'];

        $transactionalEmail = \Mage::getSingleton('core/email_template');
        $transactionalEmail->getMail()->createAttachment(
             file_get_contents($data['attachment']['path']),
             'application/pdf',
             \Zend_Mime::DISPOSITION_ATTACHMENT,
             \Zend_Mime::ENCODING_BASE64,
             basename($data['attachment']['name'])
         );
        $transactionalEmail->addBcc(\Mage::getStoreConfig('trans_email/ident_general/email'));
       
        try {
            $transactionalEmail->sendTransactional($templateId, $sender, $emailTo, $nameTo, $vars);
        } catch (Exception $e) {
            \Mage::log($e, null, 'send_billing.log');
        }

        return $transactionalEmail->getSentSuccess();
    }

    // TABLE GENERAL FUNCTIONS

    private function _computeColumnSet($columnset)
    {
        $offset = 0;
        foreach ($columnset as $id => $w) {
            $columnset[$id] = $offset + $this->_table_padding;
            $offset += intval(round(floatval($w * ($this->_width - 2 * $this->_margin_horizontal)), 0, PHP_ROUND_HALF_UP));
        }

        return $columnset;
    }

    private function _computeTotals($table)
    {
        $index = 0;
        foreach ($table['header_type'] as $type) {
            if ($type == 'float') {
                foreach ($table['rows'] as $row) {
                    $keys = array_keys($row);
                    $table['total'][$index] += floatval($row[$keys[$index]]);
                }
            }
            ++$index;
        }

        return $table;
    }

    private function _printTableHeader($table)
    {
        $this->_currentpage->setFont($this->_font_bold, $table['config']['font_size_header']);
        $this->_currentpage->setFillColor(new \Zend_Pdf_Color_Html($table['config']['fill_color_header']));
        $this->_currentpage->setLineColor(new \Zend_Pdf_Color_Html($table['config']['line_color_header']));
        $this->_currentpage->drawRectangle($this->_margin_horizontal, $this->_offset - $this->_lineHeight, $this->_width - $this->_margin_horizontal, $this->_offset);
        $this->_currentpage->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
        $this->_offset -= $this->_lineHeight;
        foreach ($table['header'] as $id => $col) {
            $this->_currentpage->drawText($col, $this->_margin_horizontal + $this->_table_column_set[$id], $this->_offset + $this->_lineHeight / 3);
        }
        $this->_currentpage->setFont($this->_font, $table['config']['font_size_body']);
    }

    private function _printTableBody($table, $row)
    {
        $this->_currentpage->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        foreach ($table['rows'] as $r) {
            $this->_offset -= $this->_lineHeight;
            if ($this->_offset < 3 * $this->_margin_vertical) {
                $this->_currentpage = $this->_page[count($this->_page)] = $this->_pdf->newPage($this->_format);
                $this->_offset = $this->_height - 2 * $this->_margin_vertical;
                $this->_printTableHeader($table);
                $this->_currentpage->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
                $this->_currentpage->setFont($this->_font, $table['config']['font_size_body']);
                $this->_offset -= $this->_lineHeight;
            }
            $col_id = 0;
            foreach ($r as $col) {
                $col = (in_array($table['header_type'][$col_id], array('float', 'percent'))) ? round(floatval($col), 2, PHP_ROUND_HALF_UP) : $col;
                $this->_currentpage->drawText($col, $this->_margin_horizontal + $this->_table_column_set[$col_id], $this->_offset);
                ++$col_id;
            }
        }
    }

    private function _printTableTotals($table)
    {
        $this->_offset -= $this->_lineHeight;
        $this->_currentpage->setLineWidth(0.5);
        $this->_currentpage->drawLine($this->_margin_horizontal, $this->_offset, $this->_width - $this->_margin_horizontal, $this->_offset);
        $this->_currentpage->setFont($this->_font_bold, $table['config']['font_size_total']);
        $this->_offset -= $this->_lineHeight;
        foreach ($table['total'] as $id => $col) {
            $this->_currentpage->drawText($col, $this->_margin_horizontal + $this->_table_column_set[$id], $this->_offset);
        }
        $this->_currentpage->setFont($this->_font, $table['config']['font_size_total']);
    }

    private function _printTable($table)
    {
        foreach ($table as $type => $row) {
            switch ($type) {
                case 'header':
                   $this->_printTableHeader($table);
                   break;
                case 'rows':
                    $this->_printTableBody($table);
                    break;
                case 'total':
                    $this->_printTableTotals($table);
                    break;
            }
        }
    }
}
