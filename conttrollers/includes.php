<?php
	session_start();

	error_reporting(E_ALL);
	ini_set('display_errors', 'On');

	include_once("cred.php");

	define("servername",  $servername);
	define("dbusername",  $dbusername);
	define("dbpassword",  $dbpassword);
	define("dbname",  $dbname);

	include_once("config.php");
	include_once("users.php");

	$users = new users;

	class setupData extends database {
    
		function __construct() {
			$this->createTable();
			$data = array(
				array(
					"firstname" => "Marguerite",
					"lastname" => "Kilpatrick",
					"email" => "marguerite@fakemail.com",
					"password" => password_hash("RandomPassword1", PASSWORD_DEFAULT)
				),
				array(
					"firstname" => "James",
					"lastname" => "Stauffer",
					"email" => "james@fakemail.com",
					"password" => password_hash("RandomPassword2", PASSWORD_DEFAULT)
				),
				array(
					"firstname" => "Donald",
					"lastname" => "Bracco",
					"email" => "donald@fakemail.com",
					"password" => password_hash("RandomPassword3", PASSWORD_DEFAULT)
				),
				array(
					"firstname" => "Rafael",
					"lastname" => "Menard",
					"email" => "rafael@fakemail.com",
					"password" => password_hash("RandomPassword4", PASSWORD_DEFAULT)
				)
			);
	
			foreach ($data as $row) {
				$this->insert(
					"users",
					$row,
					true
				);
			}
		}
	
		private function createTable() {
			$sql = "CREATE TABLE IF NOT EXISTS `users`(
				`ref` INT NOT NULL AUTO_INCREMENT,
				`firstname` VARCHAR(100) NOT NULL,
				`lastname` VARCHAR(100) NOT NULL,
				`email` VARCHAR(255) NOT NULL,
				`password` VARCHAR(500) NOT NULL,
				`create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`login_time` DATETIME NULL,
				PRIMARY KEY(`ref`),
				UNIQUE(`email`)
			);";
			$this->query($sql);
		}
	}
?>