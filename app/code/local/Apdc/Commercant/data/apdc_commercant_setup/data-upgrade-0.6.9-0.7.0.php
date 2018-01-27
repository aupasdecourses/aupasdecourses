<?php

$shops = Mage::getModel('apdc_commercant/shop')->getCollection();

$data=array(array(1,2),
array(2,1),
array(4,3),
array(5,7),
array(6,5),
array(7,4),
array(8,6),
array(9,6),
array(10,1),
array(11,2),
array(12,3),
array(13,7),
array(14,5),
array(15,4),
array(16,1),
array(17,2),
array(18,3),
array(19,7),
array(20,5),
array(21,4),
array(22,1),
array(23,2),
array(24,3),
array(25,7),
array(26,5),
array(27,4),
array(28,2),
array(29,3),
array(30,7),
array(31,5),
array(32,4),
array(33,1),
array(34,3),
array(35,1),
array(36,4),
array(37,5),
array(38,2),
array(39,7),
array(40,2),
array(41,2),
array(42,6),
array(43,1),
array(44,4),
array(45,5),
array(46,2),
array(47,3),
array(48,7),
array(50,4),
array(51,5),
array(52,2),
array(53,3),
array(54,7),
array(55,6),
array(56,6),
array(57,1),
array(58,4),
array(59,5),
array(60,2),
array(61,2),
array(62,3),
array(63,2),
array(64,1),
array(65,7),
array(66,5),
array(67,4),
array(68,6),
array(69,2),
array(70,1),
array(71,3),
array(72,7),
array(73,5),
array(75,2),
array(76,1),
array(77,2),
array(78,7),
array(79,6),
array(80,1),
array(81,2),
array(82,7),
array(83,6),
array(84,3),
array(90,5),
array(91,4),
array(92,7),
array(93,2),
array(94,1),
array(95,2),
array(96,1),
array(97,6),
array(98,4),
array(99,2),
array(100,2),
array(101,4),
array(102,3),
array(103,3),
array(104,1),
array(105,2),
array(106,2),
array(107,5),
array(108,7),
array(109,2),
array(110,1),
array(111,6),
array(112,2),
array(114,2),
array(115,1),
array(116,2),
array(117,1));


$tmp=array();
foreach($data as $row){
	$tmp[$row[0]]=array(
		'type_shop'=>$row[1],
	);
}

foreach($shops as $shop){
	if(array_key_exists($shop->getId(), $tmp)){
		$d=$tmp[$shop->getId()];
		$shop->setData('type_shop',$d['type_shop']);
		$shop->save();
	}else{
		Mage::log($shop->getId()." not found",null,"data_commercants.log");
	}
}

