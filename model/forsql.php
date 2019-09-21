<?php
require_once './userModel.php';
$usermodel = new UserModel();
//$dao->createTable("CREATE TABLE user_profile(user_id INT(6) UNSIGNED PRIMARY KEY, pic VARCHAR(225), currency VARCHAR(225) DEFAULT 'JPY')");
//$dao->insertUser("test-a", "testa", "a@gail.com", "female");
require_once './dao.php';
$dao = new Dao();
//$dao->createTable("CREATE TABLE money_record (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id iNT(6) NOT NULL, type VARCHAR(10) NOT NULL, person VARCHAR(225) NOT NULL, status VARCHAR(225) NOT NULL, amount INT(225) NOT NULL, comment VARCHAR(225), deadline DATE)");
//var_dump($dao->select("SELECT * FROM user WHERE username = :username", array(':username' => 'yamada')));
require_once './moneyModel.php';
$moneymodel = new MoneyModel;
$moneymodel->getAllRecordsBasedOnPerson(1);
?>