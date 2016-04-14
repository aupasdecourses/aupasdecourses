<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Payment_Paypal_Log extends AwoDev_AwoRewards_Helper_Payment_Paypal {
	// FINE Logging Level
	const FINE = 3;

	// INFO Logging Level
	const INFO = 2;

	// WARN Logging Level
	const WARN = 1;

	// ERROR Logging Level
	const ERROR = 0;

	
	// Default Logging Level
	const DEFAULT_LOGGING_LEVEL = 0;
	
	// Logger name
	private $loggerName;

	// Log enabled
	private $isLoggingEnabled;

	// Configured logging level
	private $loggingLevel;

	// Configured logging file
	private $loggerFile;
	
	public function __construct() {
		$log_level = 'INFO';
		$log_filename = 'Paypal.log';
		$log_enabled = 0;
		
	
		$this->loggerName = 'Payment';
		$this->loggerFile = ($log_filename) ? $log_filename: ini_get('error_log');
		$loggingEnabled = $log_enabled;
		$this->isLoggingEnabled = (isset($loggingEnabled)) ? $loggingEnabled : false;
		$loggingLevel = strtoupper($log_level);
		$this->loggingLevel = (isset($loggingLevel) && defined("self::$loggingLevel")) ? constant("self::$loggingLevel") : 0;

	}
	
	public function log($message, $level=self::INFO) {
		if($this->isLoggingEnabled && ($level <= $this->loggingLevel)) {
			error_log( $this->loggerName . ": $message\n", 3, $this->loggerFile);
		}
	}	

	public function error($message) {
		$this->log($message, self::ERROR);
	}
	
	public function warning($message) {
		$this->log($message, self::WARN);
	}	
	
	public function info($message) {
		$this->log($message, self::INFO);
	}
	
	public function fine($message) {
		$this->log($message, self::FINE);
	} 

}