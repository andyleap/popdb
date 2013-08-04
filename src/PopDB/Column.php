<?php

namespace PopDB;

class Column
{
	const STRING = 1;
	const INTEGER = 2;
	const DECIMAL = 3;
	const DATETIME = 4;
	const DATE = 5;
	const TIME = 6;
	
	public $name;
	public $type;
	public $raw_type;
	public $length;
	public $nullable;
	public $pk;
	public $auto_increment;
	
	private $adapter;
	
	function __construct($adapter)
	{
		$this->adapter = $adapter;
	}
}
