<?php

class Database {

	private $dbhost = DB_HOST;
	private $dbname = DB_NAME;
	private $dbuser = DB_USER;
	private $dbpass = DB_PASS;

	private $db;
	private $stmt;

	public function __construct()
	{
		$con = 'mysql:host='.$this->dbhost.';dbname='.$this->dbname;

		try {
			$this->db = new PDO($con, $this->dbuser, $this->dbpass);
		}
		catch (PDOException $e) {
			die($e->getMessage());
		}
	}

	
	public function quote($var)
	{
		return $this->db->quote($var);
	}


	public function query($query)
	{
		$this->stmt = $this->db->prepare($query);
	}


	public function bind($params, $value, $type = null)
	{
		if (is_null($type))
		{
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
					break;
			}

			$this->stmt->bindValue($params, $value, $type);
		}
	}


	public function execute()
	{
		$this->stmt->execute();
	}


	public function num_rows($query)
	{
		$this->stmt = $this->db->prepare($query);
		$this->stmt->execute();
		return $this->stmt->fetchColumn();
	}


	public function rowCount()
	{
		return $this->stmt->rowCount();
	}


	public function return($func, $params = null)
	{
		return $this->stmt->$func($params);
	}

}