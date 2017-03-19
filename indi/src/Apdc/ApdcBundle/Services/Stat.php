<?php

namespace Apdc\ApdcBundle\Services;

include '../../app/Mage.php';

class Stat
{

    public function __construct()
    {
        \Mage::app();
    }

    public function end_month($date)
    {
        $date = strtotime('+1 month', strtotime(str_replace('/', '-', $date)));
        $date = strtotime('-1 second', $date);
        $date = date('Y-m-d', $date);

        return $date;
    }

    public function getNotes($date_debut, $date_fin)
    {
        $notationClient = \Mage::getModel('apdc_notation/notation')->getCollection();
        $result = array();
        foreach ($notationClient as $n) {
            $result[] = array(
                'order_id' => $n->getOrderId(),
                'note' => $n->getNote(),
            );
        }
        return $result;
    }

    public function histogramme()
    {
        $notes = $this->getNotes();
        $result = array();

        foreach ($notes as $id => $n) {
            if (!array_key_exists($n, $result)) {
                $result[$n] = 1;
            } else {
                $result[$n] += 1;
            }
        }

        return json_encode($result);
    }
}
