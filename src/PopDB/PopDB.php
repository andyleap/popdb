<?php

namespace PopDB;

use Popple\Popple;

class PopDB extends Popple
{
	private $adapter;
	
	function __construct()
	{
		$this->adapter = new Adapter\MySQL($this);
		$this['host'] = '127.0.0.1';
		$this['port'] = '3306';
	}
	
	function Connect()
	{
		$this->adapter->Connect();
	}
	
	function Test()
	{
		echo '<pre>';
		print_r($this->adapter->GetTables($this['database']));
		foreach($this->adapter->GetTables($this['database']) as $table)
		{
			print_r($this->adapter->GetColumns($this['database'], $table));
		}
		echo '</pre>';
	}
}