<?php
	class database {
		/**
		 * @param object	$db			the datavase object
		 * @param string	$table		the name of the table that is querried
		 * 
		 */

        var $db;
        var $table;
        var $data = array();
        var $where = array();

        var $prepare = array();
        var $query;

		function connect() {
			$db = new PDO('mysql:host='.servername.';dbname='.dbname.';charset=utf8', dbusername, dbpassword, 
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return $db;
        }

		/**
		 * @param string	$table		name of table to be inserted into
		 * @param array		$data		the array containing the key value pair corresponding to the coloum and corresponing data to be inserted, updated or replaced
		 * @param array		$where		the array containing the key value pair corresponding to the coloum and corresponing data in the WHERE clause
		 * @param array		$replace	an array containing the value key pair of the data to be replaced if exisit
		 * 
		 */
        public function insert($table, $data, $ignore = false) {
            $queryLine1 = "";
            $queryLine2 = "";

            foreach  ($data as $key => $value) {
                $queryLine1 .= "`".$key."`,";
                $queryLine2 .= ":".$key.",";

                $prepare[":".$key] = $value;
            }

            $queryLine1 = trim($queryLine1, ",");
            $queryLine2 = trim($queryLine2, ",");

            $query = "INSERT ".(($ignore === true ) ? "IGNORE " : "")."INTO `".$table."` (".$queryLine1.") VALUES (".$queryLine2.")";
            return $this->run($query, $prepare, "insert");
        }

        /**
		 * Get a row of items in a table based on the search criteria
		 * @param string $table	name of table to fetch from
		 * @param string $tag	the col to fetch from
		 * @param string $id	the row to fetch from
		 * @param string $where	any addition to the WHERE clause
        */
        public function getOne($table, $id, $tag='ref', $where=false) {
            if ($where !== false) {
                $where = " AND ".$where;
            } else {
                $where = "";
            }
            $query = "SELECT * FROM `".$table."` WHERE `".$tag."` = :".$tag.$where." LIMIT 1";
            $return[":".$tag] = $id;
            return $this->run($query, $return, "getRow");
        }

        
        /**
        *   @param string $table   =   name of table to be update
        *   @param string $tag     =   coloun to update
        *   @param string $value   =   value to update in the colounm
        *   @param string $ref     =   row to update
        *   @param string $id      =   unique colounm in row to update
        */
        public function updateOne($table, $tag, $value, $id, $ref="ref", $extra=false) {
            if ($extra !== false) {
                $extra = ", ".$extra;
            }
            $query = "UPDATE `".$table."` SET  `".$tag."` = :".$tag.$extra." WHERE `".$ref."`=:w_".$ref;
            $prepare[":".$tag] = $value;
            $prepare[":w_".$ref] = $id;

            return $this->run($query, $prepare);
        }



        /**
        *  check if a particular field has a particular distinct data
        *   @param string $table   =   name of table to be checked
        *   @param string $key     =   coloun to check for data from
        *   @param string $value   =   value to check against the key
        *   @param string $return  =   data to return
        *   @param string $col     =   col to get
        */
        public function checkExixst($table, $key, $value, $return="count", $col="ref") {
            $query = "SELECT `".$col."` FROM `".$table."` WHERE `".$key."` = :".$key;
            $prepare[":".$key] = $value;
            if ($return == "col") {
                return $this->run($query, $prepare, "getCol");
            } else {
                return $this->run($query, $prepare, "count");
            }
        }

        /*  run direct SQL queries in the database
        *   queries either pepared or raw
        *   $prepare: if query is prepared, array with the prepared values
        */
        public function query($query, $prepare=false, $type=false) {
            return $this->run($query, $prepare, $type);
        }

        /*  runs the SQL query with a prepare array for the variables
        *   Type    =   false
                    =   insert: when the query is an insert satatement
                    =   replace: when the query is an insert statement with a replace duplicate statement
                    =   list: get a list of associated array for all the rows
                    =   getRow: get one row
                    =   getCol: get one col
                    =   count: get row count
            search: the binded search word
        */
        private function run($query, $prepare=[], $type=false, $search=false) {
            $db = $this->connect();
            try {
                if ($prepare != false) {
                    if ($search != false) {
                        $sql = $db->prepare($query);

                        foreach ($prepare as $key => $value) {
                            if ($key == ":".$search) {
                                $sql->bindValue($key, "%".$value."%");
                            } else {
                                $sql->bindValue($key, $value);
                            }
                        }
                        $sql->execute();
                    } else {
                        $sql = $db->prepare($query);
                        $sql->execute($prepare);
                    }
                } else {
                    $sql = $db->query($query);
                }
			} catch(PDOException $ex) {
                if (strpos($ex->getMessage(), "Base table or view not found") !== false) {
                    return "-1";
                } else {
                    exit( "An Error occured! ".$ex->getMessage() );
                }
            }
            if ($sql) {
                if ($type == "replace") {
                    return ($db->lastInsertId('ref') > 0 ? $db->lastInsertId('ref') : $prepare[":ref"]);
                } else if ($type == "insert") {
                    return $db->lastInsertId('ref');
                } else if ($type == "list") {
                    return $sql->fetchAll(PDO::FETCH_ASSOC);
                } else if ($type == "getRow") {
                    return $sql->fetch(PDO::FETCH_ASSOC);
                } else if ($type == "getCol") {
                    return $sql->fetchColumn();
                } else if ($type == "count") {
                    return $sql->rowCount();
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }
?>