<?php
	class Core {

		/** Connection info */
		private $database_types = array("sqlite2","sqlite3","sqlsrv","mssql","mysql","pg","ibm","dblib","odbc","oracle","ifmx","fbd");
		public $host;
		public $database;
		public $user;
		public $password;
		private $port;
		private $database_type;
		private $root_mdb;

		/** Variable $sql, query string to execute. */
		private $sql;
		private $con;
		private $err_msg = "";
		

		public function __construct(){
			$this->database_type = "mysql";

      //local
      // $this->host     = "localhost";
      // $this->database = "vates";
      // $this->user     = "root";
      // $this->password = "";
      // $this->port     = "";
      
      //heroku
      $this->host     = "us-cdbr-iron-east-04.cleardb.net";
      $this->database = "heroku_79b2a01cefddbe9";
      $this->user     = "bc576d59dd7749";
      $this->password = "fb544929";
      $this->port     = "";
      
			
			$this->connect();
		}


		public function connect( ){
				try{
					
					$this->con = (is_numeric($this->port)) ? new PDO("mysql:host=".$this->host.";port=".$this->port.";dbname=".$this->database, $this->user, $this->password) : new PDO("mysql:host=".$this->host.";dbname=".$this->database, $this->user, $this->password);
					$this->con->exec( "set names utf8" );
					$this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					return $this->con;

				}catch(PDOException $e){

					print_r("<b>Erro ao estabelecer conexão:</b> ". $e->getMessage() );
					exit;

				}
		}	
		
		public function query( $sql_statement ){
			$this->err_msg = "";
			if($this->con!=null){
				try{
					$this->sql=$sql_statement;
					return $this->con->query($this->sql);
				}catch(PDOException $e){
					$this->err_msg = "Error: ". $e->getMessage();
					return false;
				}
			}else{
				$this->err_msg = "Error: Connection to database lost.";
				return false;
			}
		}

		public function rollBack( ){
			$this->con->rollBack( );
		}

		public function beginTransaction( ){
			$this->con->beginTransaction( );
		}

		public function commit( ){
			$this->con->commit( );
    }

    public function inTransaction( ){
      return $this->con->inTransaction() ;
    }

		public function query_first( $sql ){
			$this->err_msg = "";
			if( $this->con!=null ){
				try{
					$this->sql = $sql;
				  $result = $this->con->query( $this->sql );
					return $result->fetch( PDO::FETCH_ASSOC );
				}catch( PDOException $e ){
					$this->err_msg = "Error: ". $e->getMessage( );
					return false;
				}
			}else{
				$this->err_msg = "Error: Connection to database lost.";
				return false;
			}
		}


		public function execute( $sql ){
			$this->err_msg = "";
			if( $this->con!=null ){
				try{
					$this->con->exec( $sql );
					return true;
				}catch( PDOException $e ){
					$this->err_msg = "Error: ". $e->getMessage( );
					return false;
				}
			}else{
				$this->err_msg = "Error: Connection to database lost.";
				return false;
			}
		}

		public function getError( ){
			return trim( $this->err_msg)!="" ? $this->err_msg : "";
		}


		public function lastInsertId( ){
			return $this->con->lastInsertId( ) ;
		}


		function validateString( $oldString ){
		
		$oldString = str_replace( "\\", "\\\\", $oldString ) ;
		
		$FIND=array("<",">","'" );
		$REPLACE=array("&lt;","&gt;","\'" );
		
		$newString = str_replace($FIND,$REPLACE,$oldString);
		
		return $newString;
		}

		public function serializeRegister( $register, $excluded_fields ){
			
			if( !is_array( $excluded_fields ) ){
				$excluded_fields = array( ) ;
			}		
			
			if(is_array($register)){ 
				
				$return = array();
				$fields = "";
				$values = "";
				
				foreach($register as $field => $value){
					if(!in_array($field, $excluded_fields)){

						# Campos
						if ($fields == ''){
							$fields = $field;
						}else{
							$fields .= ", ".$field;
						}


						if ( strtoupper( $value ) === "NULL" || $value === null || $value === "" ) {
							$value = "NULL" ;
						} else {
							$value = "'".$this->validateString( $value )."'";
						}

						# Valores
						if ($values == ''){
							$values = $value ;
						}else{
							$values .= ", ".$value ;
						}						
					}			   
				}  
				
				$return["fields"] = $fields;
				$return["values"] = $values;
				
				return $return;  
			}			
		}

		public function prepareUpdateQuery( $vo, $excludeFields ){
			
			$updateSQL = "" ;
			$isFirst = true ;
			
			foreach( $vo as $key => $value ){
				
				if( !in_array( $key, $excludeFields ) ){
					if( !$isFirst ){					
						$updateSQL .= ", 
						" ;					
					}
					
					if ( $value === "NULL" ) {
						$updateSQL .= " ".$key." = NULL" ;
					} else {
						$updateSQL .= " ".$key." = '".$this->validateString( $value )."'" ;
					}
					
					$isFirst = false ;
				}
			}
			
			return $updateSQL ;
    }
    
    /*gamby :-) by Guilherme Lobo 15/02/2018 */
    public function getLatestId($table, $field){
      $this->err_msg = "";
      $sql_statement = "";
      $dbtype = $this->database_type;
  
      if($dbtype=="sqlsrv" || $dbtype=="mssql" || $dbtype=="ibm" || $dbtype=="dblib" || $dbtype=="odbc"){
        $sql_statement = "SELECT TOP 1 ".$field." FROM ".$table." ORDER BY ".$field." DESC;";
      }elseif($dbtype=="oracle"){
        $sql_statement = "SELECT ".$field." FROM ".$table." WHERE ROWNUM<=1 ORDER BY ".$field." DESC;";
      }elseif($dbtype=="ifmx" || $dbtype=="fbd"){
        $sql_statement = "SELECT FIRST 1 ".$field." FROM ".$table." ORDER BY ".$field." DESC;";
      }elseif($dbtype=="mysql" || $dbtype=="sqlite2" || $dbtype=="sqlite3"){
        $sql_statement = "SELECT ".$field." FROM ".$table." ORDER BY ".$field." DESC LIMIT 1;";
      }elseif($dbtype=="pg"){
        $sql_statement = "SELECT ".$field." FROM ".$table." ORDER BY ".$field." DESC LIMIT 1 OFFSET 0;";
      }
  
      if($this->con!=null){
        try{
          return $this->query_single($sql_statement);
        }catch(PDOException $e){
          $this->err_msg = "Error: ". $e->getMessage();
          return false;
        }
      }else{
        $this->err_msg = "Error: Connection to database lost.";
        return false;
      }
    }

    public function query_single($sql_statement){
      $this->err_msg = "";
      if($this->con!=null){
        try{
          $sttmnt = $this->con->prepare($sql_statement);
          $sttmnt->execute();
          return $sttmnt->fetchColumn();
        }catch(PDOException $e){
          $this->err_msg = "Error: ". $e->getMessage();
          return false;
        }
      }else{
        $this->err_msg = "Error: Connection to database lost.";
        return false;
      }
    }

    public function disconnect(){
      $this->err_msg = "";
      if($this->con){
        $this->con = null;
        return true;
      }else{
        $this->err_msg = "Error: Connection to database lost.";
        return false;
      }
    }
  

	}
?>