<?php

class Dao{
	const DSN = 'mysql:dbname=heroku_5d86ffe5df9a291;host=us-cdbr-iron-east-02.cleardb.net;charset=utf8;';
	const USERNAME = 'b2b07fb24265d9';
	const PASSWORD = '64e45a57';

	private $db_instance = null;

	public function __construct(){
		$options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8'");

		error_reporting(E_ALL & ~E_NOTICE);

		try{
			$this->db_instance = new PDO(self::DSN, self::USERNAME, self::PASSWORD, $options);
			$this->db_instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e){
			echo $e->getMessage();
			exit;
		}
	}

	public function createTable($sql){
		if($this->db_instance->query($sql) == true){
			echo "created successfully";
		} else {
			echo "not created seccessfully";
		}
	}

	public function insert($sql, array $arr){
		$stmt = $this->db_instance->prepare($sql);
		$stmt->execute($arr);
		return true;
	}

	public function select($sql, array $arr){
		$stmt = $this->db_instance->prepare($sql);
		$stmt->execute($arr);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function selectMultipulData($sql, array $arr){
		$stmt = $this->db_instance->prepare($sql);
		$stmt->execute($arr);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function update($sql, array $arr){
		$stmt = $this->db_instance->prepare($sql);
		$stmt->execute($arr);
	}

	public function delete($sql, array $arr){
		$stmt = $this->db_instance->prepare($sql);
		$stmt->execute($arr);
	}

}
?>