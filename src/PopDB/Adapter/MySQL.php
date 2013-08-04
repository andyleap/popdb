<?php

namespace PopDB\Adapter;

use PDO;
use PopDB;
use PopDB\Column;

class MySQL Implements Base
{
	private $connection;
	private $cache = array();
	private $p;
	
	private static $TypeMap = array(
		'INT' => Column::INTEGER,
		'VARCHAR' => Column::STRING,
		'CHAR' => Column::STRING
	);
	
	function __construct($popple)
	{
		$this->p = $popple;
	}
	
	private function quoteObject($object)
	{
		return '`' . $object . '`';
	}
	
	public function Connect()
	{
		if(!isset($this->p['host']) || !isset($this->p['port']) || !isset($this->p['username']) || !isset($this->p['password']))
		{
			throw new \BadMethodCallException('Missing database connection info');
		}
		
		$dsn = 'mysql:host=' . $this->p['host'] . ';port=' . $this->p['port'];
		
		$this->connection = new PDO($dsn, $this->p['username'], $this->p['password']);
		$this->cache = array();
	}
	
	public function IsConnected()
	{
		if($this->connection == null)
		{
			return false;
		}
		return true;
	}

	public function GetAll($query, $assoc = true, $parameters = array())
	{
		if(!$this->IsConnected())
		{
			$this->Connect();
		}
		if(!array_key_exists($query, $this->cache))
		{
			$this->cache[$query] = $this->connection->prepare($query);
		}
		$pquery = $this->cache[$query];
		$pquery->execute($parameters);
		$data = $pquery->fetchAll(($assoc) ? PDO::FETCH_ASSOC : PDO::FETCH_NUM);
		$pquery->closeCursor();
		return $data;
	}

	public function GetTables($database)
	{
		return array_map(
			function($r){ 
				return $r[0]; 
			}, $this->GetAll('SHOW TABLES FROM ' . $this->quoteObject($database), false));
	}
	
	public function GetColumns($database, $table)
	{
		return array_map(
			function($coldata)
			{
				$col = new Column($this);
				$col->name = $coldata['Field'];
				preg_match('/([a-zA-Z]+)(\([\d]+\))?/', $coldata['Type'], $matches);
				$col->raw_type = strtoupper($matches[1]);
				$col->length = $matches[2];
				$col->nullable = $coldata['Null'] == 'YES';
				$col->pk = $coldata['Key'] == 'PRI';
				$col->auto_increment = strstr($coldata['Extra'], 'auto_increment') !== FALSE;
				$col->type = self::$TypeMap[$col->raw_type];
				return $col;
			},
			$this->GetAll('DESCRIBE ' . $this->quoteObject($database) . '.' . $this->quoteObject($table) . ''));
	}

	public function RunQuery($query)
	{
		if(!$this->IsConnected())
		{
			$this->Connect();
		}
		if(!array_key_exists($query, $this->cache))
		{
			$this->cache[$query] = $this->connection->prepare($query);
		}
		$pquery = $this->cache[$query];
		$pquery->execute($parameters);
		$pquery->closeCursor();
	}

	public function CastFromDB($type, $raw_type, $value)
	{
		switch($type)
		{
			case Column::STRING:
				return (string) $value;
			case Column::INTEGER:
				return (integer) $value;
			case Column::DECIMAL:
				return (double) $value;
			case Column::DATETIME:
				return new DateTime($value);
			case Column::DATE:
				return new DateTime($value);
			case Column::TIME:
				return new DateTime($value);
		}
	}

	public function CastToDB($type, $raw_type, $value)
	{
		switch($type)
		{
			case Column::STRING:
				return (string) $value;
			case Column::INTEGER:
				return (integer) $value;
			case Column::DECIMAL:
				return (double) $value;
			case Column::DATETIME:
				return $value->format('Y-m-d H:i:s');
			case Column::DATE:
				return $value->format('Y-m-d');
			case Column::TIME:
				return $value->format('H:i:s');
		}
	}
}

