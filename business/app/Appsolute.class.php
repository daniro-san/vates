<?php

class Appsolute{

  const CODE_SECURITY_ISSUES = 50               ;
  const CODE_BUSINESS_ERROR  = 998              ;
  const CODE_SERVICE_SUCCESS = 0                ;
  const SERVICE_TYPE         = "vates" ;
	
	private $core   = null ;
	// private $logger = null ;
	
	public function __construct( $core ){
		$this->core = $core ;
	}
	
	// public function setLogger( $logger ){
	// 	$this->logger = $logger ;
  // }

	// public function getLogger( ){
	// 	return $this->logger ;
	// }
	
	public function generateReturnBusiness( $data, $statusCod, $message ){
		$return = array( ) ;
		
		$return["status"]  = $statusCod ;
		$return["message"] = $message ;
		
		if( isset( $data) ){
			$return["DATA"] = $data ;
		}
		
		return $return ;
	}
	
	function findFK( $table, $erpId ){
		
		if( $erpId == '' || !isset( $erpId ) ){
			return 0 ;
		}
		
		$sql = "select ID from ".$table." where erp_id = ".$erpId ;
		$result = $this->core->query_first( $sql ) ;
		
		if( !$result ){
			return false ;			
		}
		
		if( $result["ID"] != '' ){			
			return( $result["ID" ] ) ;
		}else{
			return -1 ;
		}		
	}
	
	public function response( $json ){

    if( $json["status"] != self::CODE_SERVICE_SUCCESS ){
      $this->core->rollBack( ) ;
    }
    else{
      $this->core->commit( ) ;
    }

		// $this->logger->closelog( $json ) ;
		header( "Content-Type: application/json" ) ;
		echo json_encode( $json, JSON_NUMERIC_CHECK ) ;
		exit ;
	}
	
	public function exists( $table, $fields, $register, $accountId ){
		
		if( $fields == '' || !isset( $fields ) ){
			return false ;
		}
		
		if( is_array( $fields ) ){
			$filterSQL = $this->prepareFilterOR( $fields, $register, $accountId ) ;
		}else{
			$filterSQL = "
				where ".validateString( $field )." = '".validateString( $value )."'" ;
		}
		
		$sql = "select 
					ID 
				from ".validateString( $table )."
				".$filterSQL."
				limit 1" ;
				
		$result = $this->core->query_first( $sql ) ;
		
		if( !$result ){
			return false ;
		}
		
		if( $result["ID"] == '' ){			
		
			return false ;
			
		}
			
		return true ;
		
	}
	
	private function prepareFilterOR( $fields, $register, $accountId ){
		
		$isFirst = true ;
		
		foreach( $fields as $key => $value ){
			
			if( !$isFirst )
				$or .= " or " ;
			else
				$isFirst = false ;
			
			$or .= "
				   ".validateString( $value )." = '".validateString( $register[$value] )."'" ;
				
		}
		
		$filterSQL = "
				where 1=1
				and (
					".$or."
				)
				and ACCOUNT_ID = ".$accountId ;
				
		return $filterSQL ;
	}
	
	function findErpId( $table, $id ){
		
		if( $id == '' || !isset( $id ) ){
			return 0 ;
		}
		
		$sql = "select ERP_ID from ".$table." where ID = ".$id ;
		$result = $this->core->query_first( $sql ) ;
		
		if( !$result ){
			return false ;			
		}
		
		if( $result["ERP_ID"] != '' ){			
			return( $result["ERP_ID" ] ) ;
		}else{
			return -1 ;
		}		
	}
	
	public function getMonthDescription( $mes ){
		
		$values = explode( "-", $mes ) ;
		
		$ano = $values[0] ;
		$mes = $values[1] ;
		$descricao = "" ;
		
		switch( $mes ){
			case "01": $descricao = "Jan" ; break ;
			case "02": $descricao = "Fev" ; break ;
			case "03": $descricao = "Mar" ; break ;
			case "04": $descricao = "Abr" ; break ;
			case "05": $descricao = "Mai" ; break ;
			case "06": $descricao = "Jun" ; break ;
			case "07": $descricao = "Jul" ; break ;
			case "08": $descricao = "Ago" ; break ;
			case "09": $descricao = "Set" ; break ;
			case "10": $descricao = "Out" ; break ;
			case "11": $descricao = "Nov" ; break ;
			case "12": $descricao = "Dez" ; break ;
		}		
		
		return $descricao ;
	}
	
	public function findStartAppsolute( ){
		
		$sql = "select VALOR from config where CHAVE = 'APPSOLUTE_START' " ;
		
		$result = $this->core->query_first( $sql ) ;
		
		return $result["VALOR"] ;
		
	}
	
	public function subOneMonthFilter( $filter ){
		
		$month = $filter->getMes( ) ;
		
		$split = explode( "-", $month ) ;
		$year  = intval( $split[0] ) ;
		$month = intval( $split[1] ) ;
		
		if( $month == 1 ){
			
			$year = $year - 1 ;
			$filter->setMes( $year."-12" ) ;
			
		}else{
			
			$month = $month - 1 ;
			
			if( $month < 10 ){
				$strMonth = "0".$month ;
			}else{
				$strMonth = $month ;	
			}
			
			$filter->setMes( $year."-".$strMonth ) ;			
		}
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
						$value = "'".validateString( $value )."'";
					}
					
				   # Valores
				   if ($values == ''){
					  $values = $value;
				   }else{
					  $values .= ", ".$value;
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
					$updateSQL .= " ".$key." = '".validateString( $value )."'" ;
				}
				
				$isFirst = false ;
			}
		}
		
		return $updateSQL ;
	}
	
	public function filterIn( $vo ){
		
		$inFilter = "" ;
		$isFirst = true ;
		
		foreach( $vo as $key => $value ){
			
			if( $isFirst ){					
				$inFilter .= " ( " ;
			}else{
				$inFilter .= ", " ;
			}
			
			if ( $value !== "NULL" ) {
				$inFilter .= "'". validateString( $value ) ."'" ;
			}
			
			$isFirst = false ;
		}
		
		$inFilter .= " ) " ;
		
		return $inFilter ;
		
		
		
	}
	
	
	public function serializeGroupBy( $vo ){
		
		$inFilter = "" ;
		$isFirst = true ;
		
		foreach( $vo as $key => $value ){
			
			if( $isFirst ){					
				$inFilter .= " " ;
			}else{
				$inFilter .= ", " ;
			}
			
			if ( $value !== "NULL" ) {
				$inFilter .= "".$value."" ;
			}
			
			$isFirst = false ;
		}
		
		$inFilter .= " " ;
		
		return $inFilter ;
		
		
		
	}
	
	public function nkCrypt($password){
		$key = '15|24|22|13|26|30|10';
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $password, MCRYPT_MODE_CBC, md5(md5($key))));
	}

	public function nkDecrypt($password){
		$key = '15|24|22|13|26|30|10';
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($password), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	}
	
	public function getAccountId( $credentialId ){
		
		$sql = "select account_id ACCOUNT_ID from credential where id = ".$credentialId ;
		
		$result = $this->core->query_first( $sql ) ;
		
		if( $result["ACCOUNT_ID"] == "" ){
			return false ;
		}
		
		return $result["ACCOUNT_ID"] ;
  }
  
  public function warning( $msg, $title = "Atenção" ){
		
		$json["status"]           = self::CODE_BUSINESS_ERROR  ;
		$json["message"]["text"]  = $msg ;
		$json["message"]["type"]  = WARNING ;
		$json["message"]["title"] = $title ;
		
		$this->response( $json ) ;
		
	}
	
	public function businessError( $msg, $debug = array( ) ){
		
		$json["status"]           = __LINE__  ;
		$json["message"]["text"]  = $msg ;
		$json["message"]["type"]  = DANGER ;
		$json["message"]["title"] = "Houve um Erro" ;
		
		$json["debug"]            = $debug ;
		$this->response( $json ) ;
		
	}
	
	public function debug( $msg ){
		
		$json["status"]  = -123 ;
		$json["message"] = "Appsolute DEBUG= " . $msg ;
		
		$this->response( $json ) ;	
		
	}
	
	public function addMonths( $date, $qtdeMonths ){
		
		$sql = "select DATE_ADD('".$date."', INTERVAL ".$qtdeMonths." MONTH ) _DATE from dual " ;
		
		$result = $this->core->query_first( $sql ) ;
		
		return( $result["_DATE"] ) ;
	}
	
	public function findAccountId( $credentialId ){
		
		$accountId = $this->getAccountId( $credentialId ) ;
		
		if( !$accountId ){
			$json["status"]  = 50 ;
			$json["message"] = "Security Issues" ;
			
			$this->response( $json ) ;
		}

		return $accountId ;
	}	
	
	public function verifyExistsAccountId( $accountId ){
		
		$sql = "select 1 HAS_ACCOUNT from account where id = ".$accountId ;
		
		$result = $this->core->query_first( $sql ) ;
		
		if( $result["HAS_ACCOUNT"] != 1 ){
			return false ;
		}

		return true ;
	}
	
	public function reportSecurityIssues( $detail = "Security Issues." ){
		
		$json["status"]           = self::CODE_SECURITY_ISSUES  ;
		$json["message"]["text"]  = $detail ;
		$json["message"]["type"]  = DANGER ;
		$json["message"]["title"] = "Alerta" ;
		
		$this->response( $json ) ;
		
	}
	
	public function getSystemURL( ){
		
		$urlMain = $_SERVER["HTTP_ORIGIN"] ;
		
		return $urlMain ;
		
	}
	
	public function validateFilter( $verifyArray, $filterArray ){
		
		foreach( $verifyArray as $key => $value ){
			if ( !array_key_exists( $value, $filterArray ) ) 
				return false ;
		}
	
		return true ;
		
	}
	
	public function specificKeys( $array , $specificKeys ){
		$result = array( ) ;
		foreach( $specificKeys as $key ){
			if( array_key_exists( $key, $array ) ){
				$result[$key] = $array[$key] ;
			}
		}
		return $result ;
	}
	
	public function listUniqueField( $array, $field ){
		$return = array( ) ;
		foreach( $array as $key => $value ){
			if( array_key_exists( $field, $value ) ){
				array_push( $return, $value[ $field ] ) ;
			}
		}
		return $return ;
	}
	
	public function isJson( $string ){
		return is_array( json_decode( $string, true ) ) ;
	}
}
?>