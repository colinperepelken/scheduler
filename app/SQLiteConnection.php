<?php
namespace App;

/**
* SQLite connection
*/
class SQLiteConnection {
	/**
	* PDO instance
	* @var type
	*/
	private $pdo;
	
	/**
	* return an instance of PDO object to connect to DB
	* @return \PDO
	*/
	public function connect() {
		if($this->pdo == null) {
			try {
				$this->pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);
			} catch (\PDOException $e) {
				echo $e->getMessage();
			}
		}
		return $this->pdo;
	}
}


?>