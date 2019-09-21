<?php
require_once 'dao.php';

class MoneyModel{

	private $dao = null;
	private $currency_list = array('JPY', 'USD', 'EUR', 'SEK');

	public function __construct(){
		$this->dao = new Dao();
	}

	public function getCurrencyList(){
		return $this->currency_list;
	}

	public function registerMoneyRecord($userid, $type, $person, $status, $amount, $currency, $comment, $deadline){
		$sql = 'INSERT INTO money_record (user_id, type, person, status, amount, currency, comment, deadline)
				VALUES (:userid, :type, :person, :status, :amount, :currency, :comment, :deadline)';
		$arr = array(
			':userid' => $userid,
			':type' => $type,
			':person' => $person,
			':status' => $status,
			':amount' => $amount,
			':currency' => $currency,
			':comment' => $comment,
			':deadline' => $deadline
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

	public function getRecordBasedOnPerson($userid, $person){
		$sql = 'SELECT type, status, amount, currency, comment, deadline, reg_date FROM money_record WHERE user_id = :userid AND person = :person ORDER BY id DESC';
		$arr = array(
			':userid' => $userid,
			':person' => $person
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		return $result;
	}

	public function calculationTotalAmount($result){
		$totalAmount = 0;
		foreach ($result as $item) {	
			if($item['status'] == '未清算'){
				if($item['type'] == '貸し'){
					$amount =intval($item['amount']);
					$totalAmount += $amount;
				}else{
					$amount =intval($item['amount']);
					$totalAmount -= $amount;
				}
			}else{
				continue;
			}
		}

		
		return $totalAmount;
	}

	public function getAllRecordsBasedOnPerson($userid){
		$personsList = $this->getPersonsList($userid);
		$result = array();
		foreach ($personsList as $person) {
			$record = $this->getRecordBasedOnPerson($userid, $person);
			array_push($result, $record);
		}

		$data = array();
		foreach ($result as $item) {
			$total = $this->calculationTotalAmount($item);
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

}

?>