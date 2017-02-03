<?php

/**
 * Class Apdc_Commercant_Helper_Data
 */
class Apdc_Commercant_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getDays()
    {
        return ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    }

    public function formatDays($days, $string=false){
    	$labeldays=$this->getDays();
        $rsl=[];
        foreach($days as $day){
            $rsl[]=$labeldays[$day-1];
        }

        if ($string){
           	$r=implode(", ", $rsl);
        }else {
        	$return=$rsl;
        }
        return $r;
    }

}
