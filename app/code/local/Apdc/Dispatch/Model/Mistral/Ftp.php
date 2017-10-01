<?php

class Apdc_Dispatch_Model_Mistral_Ftp extends Mage_Core_Model_Abstract
{
	protected $_path;
	protected $_host;
	protected $_port;
	protected $_ssl;
	protected $_connection;

	protected $_dos;
	protected $_maxlengths;
	protected $_creneaux;

	public function __construct() {

		$this->_host = "ftporamtl.stars-services.com";
		$this->_port = 21;
		$this->_ssl = false;

		$this->_path=Mage::getBaseDir()."/var/tmp/";

		$dos=Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();
		$dos=$dos->toArray(array('code_do','website_id'))['items'];
		foreach($dos as $do){
			$this->_dos[$do['website_id']]=$do['code_do'];
		}

		$this->_maxlengths = [
			1=>3,
			2=>10,
			3=>15,
			4=>40,
			5=>64,
			6=>128,
			7=>10,
			8=>20,
			9=>128,
			10=>5,
			11=>60,
			12=>2,
			13=>60,
			14=>10,
			15=>10,
			16=>20,
			17=>10,
			18=>3,
			19=>1,
			20=>10,
			21=>5,
			22=>40,
			23=>5,
			24=>5,
			25=>5,
			26=>15,
			27=>20,
			28=>9,
			29=>9,
			30=>5,
			31=>10,
			32=>3,
			33=>9,
			34=>20,
			35=>1,
			36=>600,
			37=>10,
			38=>40,
			39=>64,
			40=>128,
			41=>10,
			42=>20,
			43=>128,
			44=>5,
			45=>60,
			46=>255,
			47=>60,
			48=>15,
			49=>10,
			50=>1,
			51=>1,
			52=>30,
			53=>30,
			54=>320,
			55=>1,
		];

		$this->_creneaux=[
			"debut" => [
				"Tue"=>'16:00',
				"Wed"=>'16:00',
				"Thu"=>'16:00',
				"Fri"=>'16:00',
				"Sat"=>"11:00",
			],
			"fin" 	=> [
				"Tue"=>'18:00',
				"Wed"=>'18:00',
				"Thu"=>'18:00',
				"Fri"=>'18:00',
				"Sat"=>"12:00",
			],
		];

	}

	protected function login($username, $password) {
		if (!ftp_login($this->_connection, $username, $password))
			throw new Exception("Login failed on {$this->_host}:{$this->_port} with {$username}.");
	}

	protected function pasv($pasv = true) {
		ftp_pasv($this->_connection, $pasv);
	}

	protected function put($remote_file, $local_file) {
		ftp_put($this->_connection, $remote_file, $local_file, FTP_ASCII);
	}

	protected function cleansemicolons($array){
		foreach($array as $key => $value){
            $array[$key]=str_replace(';', '', $value);
        }
        return $array;
	}

	protected function co($do, $merchant, $order) {
		$co = [
			1 => 'CO2',
			2 => $do,
			3 => $order['customer_id'],
			4 => "{$order['increment_id']}-{$merchant['id_attribut_commercant']}",
			5 => '',
			6 => 'Carton',
			7 => 1,
			8 => '',
			9 => '',
			10 => '',
			11 => '',
		];

		return implode(';', $this->cleansemicolons($co));
	}

	protected function ol($do, $merchant, $order) {

		$delivery_date = DateTime::createFromFormat('Y-m-d', $order['delivery_date'])->format('d/m/Y');

			$ol = [
				1 => 'OL',
				2 => $do,
				3 => '',
				4 => $order['customer_id'],
				5 => '',
				6 => "{$order['first_name']} {$order['last_name']}",
				7 => '',
				8 => '',
				9 => $order['street'],
				10 => $order['zipcode'],
				11 => $order['city'],
				12 => 'FR',
				13 => 'FRANCE',
				14 => preg_replace('/ /', '', $order['phone']),
				15 => '',
				16 => $order['batiment'],
				17 => $order['codeporte1'],
				18 => $order['etage'],
				19 => 'N',
				20 => '',
				21 => '',
				22 => "{$order['increment_id']}-{$merchant['id_attribut_commercant']}",
				23 => preg_split('/-/', $order['delivery_time'])[0],
				24 => preg_split('/-/', $order['delivery_time'])[1],
				25 => '',
				26 => '',
				27 => '',
				28 => '',
				29 => '',
				30 => '',
				31 => $delivery_date,
				32 => '',
				33 => '',
				34 => 'TRAD',
				35 => 'C',
				36 => "{$order['info']} | Autre Contact: {$order['contact']} {$order['contact_phone']}",
				37 => '',
				38 => $order['increment_id'],
				39 => '',
				40 => '',
				41 => '',
				42 => '',
				43 => '',
				44 => '',
				45 => '',
				46 => '',
				47 => '',
				48 => '',
				49 => '',
				50 => '',
				51 => '',
				52 => '',
				53 => 'LPR',
				54 => $order['mail'],
			];

			foreach($ol as $key => $value){
				$ol[$key]=substr($value,0,$this->_maxlengths[$key]);
			}

			return implode(';', $this->cleansemicolons($ol));
		}


	protected function oe($do, $merchant, $order) {

		$datetime = DateTime::createFromFormat('Y-m-d', $order['delivery_date']);
		$day = $datetime->format('D');
		$delivery_date = $datetime->format('d/m/Y');

		$oe = [
			1 => 'OE',
			2 => $do,
			3 => '',
			4 => $merchant['id_attribut_commercant'],
			5 => $merchant['name'],
			6 => $merchant['name'],
			7 => '',
			8 => '',
			9 => $merchant['street'],
			10 => $merchant['postcode'],
			11 => $merchant['city'],
			12 => 'FR',
			13 => 'FRANCE',
			14 => preg_replace('/ /', '', $merchant['phone']),
			15 => preg_replace('/ /', '', $merchant['m_phone']),
			16 => '',
			17 => '',
			18 => '',
			19 => 'N',
			20 => '',
			21 => '',
			22 => "{$order['increment_id']}-{$merchant['id_attribut_commercant']}",
			23 => $this->_creneaux['debut'][$day],
			24 => $this->_creneaux['fin'][$day],
			25 => '',
			26 => '',
			27 => '',
			28 => '',
			29 => '',
			30 => '',
			31 => $delivery_date,
			32 => '',
			33 => '',
			34 => 'TRAD',
			35 => 'C',
			36 => '',
			37 => '',
			38 => $order['increment_id'],
			39 => '',
			40 => '',
			41 => '',
			42 => '',
			43 => '',
			44 => '',
			45 => '',
			46 => '',
			47 => '',
			48 => '',
			49 => '',
			50 => '',
			51 => '',
			52 => '',
			53 => 'LPR',
			54 => 'contact@aupasdecourses.com',
		];

		foreach($oe as $key => $value){
			$oe[$key]=substr($value,0,$this->_maxlengths[$key]);
		}

		return implode(';', $this->cleansemicolons($oe));
	}

	protected function formatFtpMistral($shops){
        $dos = $this->_dos;
		$out = "";
		foreach($shops as $store_id => $store){
			foreach($store as $id => $shop) {
				foreach($shop['orders'] as $i => $o) {
					if(isset($shop['infos'])){
						$out .= $this->oe($dos[$store_id], $shop['infos'], $o).";0".PHP_EOL;
						$out .= $this->ol($dos[$store_id], $shop['infos'], $o).";0".PHP_EOL;
						$out .= $this->co($dos[$store_id], $shop['infos'], $o).";0".PHP_EOL;
					}else{
						$prods="";
						foreach($o['products'] as $prod){
							$prods.="commercant. ".$prod['commercant']. ', item_id: '.$prod['item_id'].PHP_EOL;
						}
						$error[]=[
							'increment_id' => $i,
							'products'=>$prods,

						];						
					}
				}
			}
		}
		if(isset($error)){
			Mage::getModel('apdcadmin/mail')->warnErrorCommercantNeighborhood($error);
		}
		return $out;
    }

	protected function _processRequestFtp($params){
		$c_time = date("His");
		$fileName = str_replace("-", "", $params["c_date"]) . "_APDC_CDE_{$c_time}.csv";
		$tmpFileName = $this->_path."tmp.csv";
		$out= $this->formatFtpMistral($params["orders"]);

	 	if ($out <> "") {
	 		file_put_contents($tmpFileName, $out);

	 		if(!is_null($this->_host)&&!is_null($this->_port)){
				$this->_connection = (!$this->_ssl) ? ftp_connect($this->_host, $this->_port) : ftp_ssl_connect($this->_host, $this->_port);
				if (!$this->_connection){
					throw new Exception("Couldn't connect to {$this->_host}:{$this->_port}.");
				}
			}

	 		$this->_connection->pasv(false);
	 		$this->_connection->login("ftpapdc", "ftp.1a");
	 		$this->_connection->put("IN/{$fileName}", $tmpFileName);
	 	}
	}

}