<?php

	Class Logger{
		
		const COLLECTION = "appflow_dev_log" ;
		
		private $coreMongo = null ;
		private $appDate   = null ;
		private $app       = null ;
		private $logID     = null ;
		private $generated = null ;
		
		function __construct( $coreMongo, $appDate, $app, $serverUniqueID ){
			$this->coreMongo = $coreMongo        ;
			$this->appDate   = $appDate          ;
			$this->app       = $app              ;
			$this->generateID( $serverUniqueID ) ;
			$this->generated = false             ;
		}
		
		public function getLogId( ){
			return $this->logID ;
		}
		
		private function generateID( $serverUniqueID ){
      if( isset( $_REQUEST["LOG_ID"] ) ){
        $this->logID = $_REQUEST["LOG_ID"] ;
      }
      else{
        list( $usec, $sec ) = explode( " ", microtime( ) ) ;
        $this->logID = $this->appDate->getNow( "%Y%m%d%H%i%s" ) . $serverUniqueID . md5( md5( $sec ) . md5( $usec ) ) . md5( $serverUniqueID ) ;
      }
		}
		
		public function generateLog( $content = array( ), $happtk = null ){
			
			if( $this->generated ){
				$json["status"]           = 50  ;
				$json["message"]["text"]  = self::CLASS_NAME . "::Log Already generated" ;
				$json["message"]["type"]  = DANGER ;
				$json["message"]["title"] = "Alerta" ;				
				echo json_encode( $json ) ;
				exit ;
			}
			
			session_start( ) ;
      $content["LOG_ID"]      = $this->getLogId( )                                               ;
      $content["UNIQUE_ID"]   = $_SERVER["UNIQUE_ID"]                                            ;
			$content["DATETIME"]    = $this->appDate->getNow( "%Y-%m-%d %H:%i:%s" )                    ;
			$content[ "_REQUEST" ]  = $_REQUEST                                                        ; 
			$content[ "_SERVER" ]   = $this->app->specificKeys( $_SERVER, self::fields_server_log( ) ) ;
			$content[ "_FILES" ]    = $_FILES                                                          ;
			$content[ "_SESSION" ]  = $_SESSION                                                        ;
      $content[ "_COOKIE" ]   = $_COOKIE                                                         ;
      $content["HAPPTK"]      = $happtk                                                          ;
      			
			if( $happtk !== null ){
        $content["CREDENTIAL_ID"] = $this->app->nkDecrypt( $happtk ) ;
			}
			
			$rsInsert = $this->coreMongo->insert( self::COLLECTION, $content, false ) ;
			
			if( !$rsInsert ){
				$json["status"]           = 50  ;
				$json["message"]["text"]  = self::CLASS_NAME . "::Error on insert" ;
				$json["message"]["type"]  = DANGER ;
				$json["message"]["title"] = "Alerta" ;				
				echo json_encode( $json ) ;
				exit ;
			}
			
			$this->generated = true ;
			
			return true ;
			
		}
		
		public function closeLog( $content = array( ) ){
			
			if( !$this->generated ){
				$json["status"]           = 50  ;
				$json["message"]["text"]  = self::CLASS_NAME . "::Log not generated" ;
				$json["message"]["type"]  = DANGER ;
				$json["message"]["title"] = "Alerta" ;				
				echo json_encode( $json ) ;
				exit ;
			}
			
      $where["LOG_ID"]    = $this->getLogId( )    ;
      $where["UNIQUE_ID"] = $_SERVER["UNIQUE_ID"] ;
			
			$update["RESPONSE"] = $content ;
			
			$rsUpdate = $this->coreMongo->update(  self::COLLECTION, $where, $update , false , true ) ;
			
			if( !$rsUpdate ){
				$json["status"]           = 50  ;
				$json["message"]["text"]  = self::CLASS_NAME . "::Error on updated" ;
				$json["message"]["type"]  = DANGER ;
				$json["message"]["title"] = "Alerta" ;				
				echo json_encode( $json ) ;
				exit ;
			}
			
			$this->generated = false ;
			
			return true ;
			
		}
		
		private static function fields_server_log( ){
			return array( 	"HTTP_USER_AGENT" ,
							"REDIRECT_STATUS" ,
							"REMOTE_ADDR"     ,
							"REQUEST_METHOD"  ,
							"SCRIPT_NAME"     ,
							"SERVER_ADDR"     ,
							"SERVER_PORT"     ,
							"SERVER_PROTOCOL" ,
							"UNIQUE_ID"
						) ;
		}
		
		
	}

?>