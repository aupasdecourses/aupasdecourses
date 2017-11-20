<?php

// Upgrade data only if no Neighborhoods exists
$shops = Mage::getModel('apdc_commercant/shop')->getCollection();

$data=array(array('bdm17','4750292','68433247'),
array('cdv17','4757039','67518396'),
array('frb17','4778105','59118552'),
array('rga17','4825553','02660715'),
array('pbp17','4756582','37070956'),
array('fib17','4784325','45805989'),
array('mar17','4860651','06556365'),
array('dan17','','68433247'),
array('lre17','','96749843'),
array('tdc17','','70688144'),
array('lft17','9028000','83048952'),
array('lrc17','9141312','42631870'),
array('mim17','9365435','88514212'),
array('gmd17','9935938','82698521'),
array('bms10','7076982','98374240'),
array('csa10','7157528','08634202'),
array('flb10','7077089','52671650'),
array('lvn10','7284244','45581703'),
array('mqf10','7150548','31047197'),
array('hff15','7949632','76467449'),
array('lgv15','7989320','33004250'),
array('vdo15','','16134994'),
array('bfl15','','17824575'),
array('apf15-2','8472003','45309476'),
array('moi15','9358635','45753485'),
array('lmm15','9366111','82 53 42 58'),
array('bp13','8344567','33833979'),
array('lct13','8362404','45834883'),
array('lfl13','8480923','45830330'),
array('bdt13','8481370','44974492'),
array('pbc13-2','8962417','45880181'),
array('idf13','9365908','64812708'),
array('abp13','8838915','45837078'),
array('jds16','8839251','47272890'),
array('cvs16','9027896','45059172'),
array('cut16','9560256','86071100'),
array('lillo16','8911812','47276908'),
array('pbf16','',''),
array('tac16','',''),
array('alb14','8839367','42184034'),
array('lbp14','9292243','45423574'),
array('moi14','9358785','43223413'),
array('lpa14','8839459','40449860'),
array('pdg14','8911558','70408824'),
array('abp09','9458096','45837078'),
array('avf09','9457184','44630562'),
array('cav09','9799717','44 53 07 49'),
array('adl09','9938614','73778718'),
array('lpg09','10407159','84620042'),
array('ble09','9358858','48743764'),
array('lcd07','9835894','65717705'),
array('fca7','9801864','838909'),
array('vcm07','9935868','47055352'),
array('lpd07','9566568','74303816'),
array('hcr07','9954311','14996576'),
array('plb05','9820424','43 54 03 01'),
array('lfg11','9948965','43 57 91 28'),
array('dup11','10214610','58307236'),
array('prc11','10304566','45657895'),
array('bmi11','10362003','43579415'),
array('bfr12','9938684','99 40 38 61'),
array('rsb12','9949009','52962998'),
array('vda12','10449368','89701476'),
array('lcp03','10198750','92200577'),
array('eti04','10198978','93104361'),
array('avm18','10449570','18716218'),
array('pds18','10449500','46064042'));

$tmp=array();
foreach($data as $row){
	$tmp[strtoupper($row[0])]=array(
		'email_hipay'=>$row[0]."@aupasdecourses.com",
		'mdp_hipay'=>$row[2],
	);
}

foreach($shops as $shop){
	if(array_key_exists($shop->getCode(), $tmp)){
		$d=$tmp[$shop->getCode()];
		$shop->setData('email_hipay',$d['email_hipay']);
		$shop->setData('mdp_hipay',$d['mdp_hipay']);
		$shop->save();
	}else{
		Mage::log($shop->getCode()." not found",null,"data_commercants.log");
	}
}

