<?php

namespace PopDB\Adapter;

interface Base
{
	public function Connect();
	public function IsConnected();
	public function RunQuery($query);
	
	public function GetAll($query, $assoc = true, $parameters = array());
	
	public function GetTables($database);
	public function GetColumns($database, $table);
	
	public function CastToDB($type, $raw_type, $value);
	public function CastFromDB($type, $raw_type, $value);
}
