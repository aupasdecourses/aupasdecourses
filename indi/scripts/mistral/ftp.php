<?php

class ftp {
	private $_host;
	private $_port;
	private $_connection;

	public function __construct($host, $port = 21, $ssl = false) {
		$this->_host = $host;
		$this->_port = $port;
		$this->_connection = (!$ssl) ? ftp_connect($this->_host, $this->_port) : ftp_ssl_connect($this->_host, $this->_port);
		if (!$this->_connection)
			throw new Exception("Couldn't connect to {$this->_host}:{$this->_port}.");
	}

	public function login($username, $password) {
		if (!ftp_login($this->_connection, $username, $password))
			throw new Exception("Login failed on {$this->_host}:{$this->_port} with {$username}.");
	}

	public function pasv($pasv = true) {
		ftp_pasv($this->_connection, $pasv);
	}

	public function put($remote_file, $local_file) {
		ftp_put($this->_connection, $remote_file, $local_file, FTP_ASCII);
	}
}
