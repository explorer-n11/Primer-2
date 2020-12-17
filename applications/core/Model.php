<?php

namespace applications\core;

use applications\lib\Db;

abstract class Model {
	
	public $db;

	public function __construct()
	{
		$this->db = new Db;
	}
}