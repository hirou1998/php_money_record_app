<?php
require_once 'dao.php';
require_once 'userModel.php';

class MoneyModel{

	private $dao = null;
	private $uermodel = null;
	private $currency_list = array('JPY', 'USD', 'EUR', 'SEK');
	private $exchange_rate_list = array(
		"JPY" => array(
			"USD" => 107.55,
			"EUR" => 118.52,
			"SEK" => 11.08
		),
		"USD" => array(
			"JPY" => 0.0092,
			"EUR" => 1.102,
			"SEK" => 0.103
		),
		"EUR" => array(
			"JPY" => 0.0084,
			"USD" => 0.907,
			"SEK" => 0.093,
		),
		"SEK" => array(
			"JPY" => 0.09,
			"USD" => 9.70,
			"EUR" => 10.693
		)
	);

	public function __construct(){
		$this->dao = new Dao();
		$this->usermodel = new UserModel();
	}

	public function getCurrencyList(){
		return $this->currency_list;
	}

	public function registerMoneyRecord($userid, $type, $person, $status, $amount, $currency, $comment, $deadline, $date){
		$sql = 'INSERT INTO money_record (user_id, type, person, status, amount, currency, comment, deadline, reg_date)
				VALUES (:userid, :type, :person, :status, :amount, :currency, :comment, :deadline, :reg_date)';
		$arr = array(
			':userid' => $userid,
			':type' => $type,
			':person' => $person,
			':status' => $status,
			':amount' => $amount,
			':currency' => $currency,
			':comment' => $comment,
			':deadline' => $deadline,
			':reg_date' => $date
		);
		$this->dao->insert($sql, $arr);
	}

	public function getMoneyRecord($userid){
		$sql = "SELECT id, type, person, status, amount, currency, comment, deadline, reg_date FROM money_record WHERE user_id = :userid ORDER BY id DESC" ;
		$arr = array(
			':userid' => $userid
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		return $result;
	}

	public function getMoneyRecordById($id){
		$sql = "SELECT id, type, person, status, amount, currency, comment, deadline, reg_date FROM money_record WHERE id = :id";
		$arr = array(
			':id' => $id
		);
		$result = $this->dao->select($sql, $arr);

		return $result;
	}

	public function getPersonsList($userid){
		$sql = "SELECT person FROM money_record WHERE user_id = :userid";
		$arr = array(
			':userid' => $userid
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		$personsList = array();
		foreach($result as $item){
			$newperson = $item['person'];
			if(in_array($newperson, $personsList) == false){
				array_push($personsList, $item['person']);
			}else{
				continue;
			}
		}

		return $personsList;
	}

	public function getRecordId($userid){
		$sql = 'SELECT id FROM money_record WHERE user_id = :userid ORDER BY id DESC';
		$arr = array(
			':userid' => $userid
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		$ids = array();
		foreach ($result as $item) {
			array_push($ids, $item['id']);
		}

		return $ids;
	}

	public function getRecordIdBasedOnPerson($userid, $person){
		$sql = 'SELECT id FROM money_record WHERE user_id = :userid AND person = :person ORDER BY id DESC';
		$arr = array(
			':userid' => $userid,
			':person' => $person
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		return $result;
	}

	public function makeIdListBasedOnPerson($userid){
		$personsList = $this->getPersonsList($userid);
		$idList = array();
		foreach ($personsList as $person) {
			$result = $this->getRecordIdBasedOnPerson($userid, $person);
			$arr = array();
			foreach ($result as $item) {
				foreach ($item as $key => $value) {
					array_push($arr, $value);
				}
			}
			$idList = $idList + array($person => $arr);
		}

		return $idList;
	}

	public function getRecordBasedOnPerson($userid, $person){
		$sql = 'SELECT id, type, status, amount, currency, comment, deadline, reg_date FROM money_record WHERE user_id = :userid AND person = :person ORDER BY id DESC';
		$arr = array(
			':userid' => $userid,
			':person' => $person
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		return $result;
	}

	public function calculationTotalAmount($result, $myCurrency){
		$totalAmount = 0;
		foreach ($result as $item) {	
			if($item['status'] == '未清算'){
				//exchange
				if($item['currency'] == $myCurrency){
					if($item['type'] == '貸し'){
						$amount =intval($item['amount']);
						$totalAmount += $amount;
					}else{
						$amount =intval($item['amount']);
						$totalAmount -= $amount;
					}
				}else{
					$exchange_rate = $this->calculateExchange($myCurrency, $item['currency']);
					//var_dump($exchange_rate);
					if($item['type'] == '貸し'){
						$amount =intval($item['amount'] * $exchange_rate);
						$totalAmount += $amount;
					}else{
						$amount =intval($item['amount']) * $exchange_rate;
						$totalAmount -= $amount;
					}
				}
			}else{
				continue;
			}
		}
	
		return round($totalAmount, 2);
	}

	public function getAllRecordsBasedOnPerson($userid, $myCurrency){
		$personsList = $this->getPersonsList($userid);
		$result = array();
		foreach ($personsList as $person) {
			$record = $this->getRecordBasedOnPerson($userid, $person);
			array_push($result, $record);
		}

		$data = array();
		foreach ($result as $item) {
			$total = $this->calculationTotalAmount($item, $myCurrency);
			$item = $item + array('total' => $total);
			array_push($data, $item);
		}

		return $data;
	}

	public function updateMoneyRecord($type, $person, $status, $amount, $currency, $comment, $deadline, $id){
		$sql = "UPDATE money_record SET type = :type, person = :person, status = :status, amount = :amount, currency = :currency, comment = :comment, deadline = :deadline WHERE id = :id";
		$arr = array(
			':type' => $type,
			':person' => $person,
			':status' => $status,
			':amount' => $amount,
			':currency' => $currency,
			':comment' => $comment,
			':deadline' => $deadline,
			':id' => $id
		);
		$this->dao->update($sql, $arr);
	}

	public function deleteMoneyRecord($id){
		$sql = "DELETE FROM money_record WHERE id = :id";
		$arr = array(
			':id' => $id
		);
		$this->dao->delete($sql, $arr);
	}

	public function calculateExchange($myCurrency, $recordCurrency){
		$exchange_rate = $this->exchange_rate_list[$myCurrency][$recordCurrency];

		return $exchange_rate;
	}

	public function changeToSettled($id){
		$sql = "UPDATE money_record SET status = :status WHERE id = :id";
		$arr = array(
			":status" => "清算済",
			":id" => $id
		);
		$this->dao->update($sql, $arr);
	}

}

?>