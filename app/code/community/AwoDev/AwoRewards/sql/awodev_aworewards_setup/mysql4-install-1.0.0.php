<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
 



	
$table = $installer->getConnection()
    ->newTable($installer->getTable('awodev_aworewards/locale'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('entity', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Table Name')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Table Key ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store Id')
    ->addColumn('col', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Column within table')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'The value')
    ->addIndex(
        $installer->getIdxName(
            'awodev_aworewards/locale',
            array('entity','entity_id','store_id','col'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity','entity_id','store_id','col',), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
	)
	
    ;
$installer->getConnection()->createTable($table);





$installer->run('

	CREATE TABLE IF NOT EXISTS '.$this->getTable('awodev_aworewards/rule').' (
		`id` int(16) NOT NULL auto_increment,
		`website_id` int(16) NOT NULL DEFAULT 1,
		`rule_name` varchar(255) NOT NULL default "",
		`rule_type` enum("registration","review","order","facebook_like","facebook_wall","twitter_tweet","twitter_follow") NOT NULL,
		`customer_type` enum("everyone","sponsor","friend") NOT NULL,
		`credit_type` enum("mage_coupon","points") NOT NULL,
		`template_id` int(11),
		`profile_id` int(11),
		`points` decimal(12,5),
		`startdate` DATETIME,
		`expiration` DATETIME,
		`published` TINYINT NOT NULL DEFAULT 1,
		`note` TEXT,
		`params` TEXT,
		`ordering` INT NOT NULL DEFAULT 0,
		PRIMARY KEY  (`id`)
	);
	
	CREATE TABLE IF NOT EXISTS '.$this->getTable('awodev_aworewards/invitation').' (
		`id` int(16) NOT NULL AUTO_INCREMENT,
		`website_id` int(16) NOT NULL DEFAULT 1,
		`invitation_name` varchar(255) NOT NULL DEFAULT "",
		`invitation_type` enum("email","facebook","twitter") NOT NULL,
		`coupon_template` int(11) DEFAULT NULL,
		`coupon_expiration` int(11) DEFAULT NULL,
		`published` tinyint(4) NOT NULL DEFAULT 1,
		`params` text,
		`ordering` int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`)
	);
	
	CREATE TABLE IF NOT EXISTS '.$this->getTable('awodev_aworewards/referral').' (
		`id` int(11) NOT NULL auto_increment,
		`website_id` int(16) NOT NULL DEFAULT 1,
		`user_id` int(11) NOT NULL default 0,
		`email` varchar(255) NOT NULL default "",
		`send_date` datetime,
		`last_sent_date` datetime,
		`invitation_id` INT,
		`customer_msg` text,
		`coupon_code` VARCHAR(50),
		`join_user_id` INT,
		`join_date` datetime,	
		`ip` varchar(15),
		`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (`id`)
	);

	CREATE TABLE IF NOT EXISTS '.$this->getTable('awodev_aworewards/credit').' (
		`id` int(11) NOT NULL auto_increment,
		`user_id` int(11) NOT NULL,
		`credit_type` enum("mage_coupon","points") NOT NULL,
		`entry_type` enum("system","admin") NOT NULL DEFAULT "system",
		`rule_id` INT,
		`rule_type` enum("registration","review","order","facebook_like","facebook_wall","twitter_tweet","twitter_follow"),
		`customer_type` enum("everyone","sponsor","friend"),
		`referral_id` int(11),
		`coupon_id` int(11),
		`item_id` varchar(255),
		`points` DECIMAL(12,5), 
		`points_paid` DECIMAL(12,5), 
		`payment_id` int(11),
		`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`note` VARCHAR(255),
		PRIMARY KEY  (`id`)
	) ;

	CREATE TABLE IF NOT EXISTS '.$this->getTable('awodev_aworewards/payment').' (
		`id` int(16) NOT NULL auto_increment,
		`payment_type` enum("mage_coupon","paypal") NOT NULL,
		`user_id` int(16) NOT NULL,
		`coupon_id` INT,
		`amount_paid` DECIMAL(12,5), 
		`payment_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`payment_details` text,
		PRIMARY KEY  (`id`)
	);

	CREATE TABLE IF NOT EXISTS '.$this->getTable('awodev_aworewards/user').' (
		`id` int(16) NOT NULL auto_increment,
		`user_id` int(16) NOT NULL,
		`url_real` VARCHAR(255),
		`url_short` VARCHAR(255),
		PRIMARY KEY  (`id`),
		UNIQUE (`user_id`)
	);
	
	
	CREATE TABLE IF NOT EXISTS '.$this->getTable('awodev_aworewards/license').' (
		`id` int(16) NOT NULL auto_increment,
		`keyname` VARCHAR(100) NOT NULL,
		`value` TEXT,
		PRIMARY KEY  (`id`),
		UNIQUE (`keyname`)
	);

');

$installer->endSetup();
