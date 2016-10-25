<?php

class db {
	private $_connection;

	public function __construct($host, $db_name, $login, $password) {
		$url = "mysql:dbname={$db_name};host={$host}";
		echo $url.PHP_EOL;
		$this->_connection = new PDO($url, $login, $password);
		$this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function queryAll($statement, $attr = []) {
		$request = $this->_connection->prepare($statement);
		$request->execute($attr);
		$request->setFetchMode(PDO::FETCH_NUM);
		return ($request->fetchAll());
	}
}
