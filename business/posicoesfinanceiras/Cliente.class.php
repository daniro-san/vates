<?php

  Class Cliente {
    private $core      = null ;
    private $app       = null ;

    public function __construct( $core, $app ){
			$this->core    = $core     ;
      $this->app     = $app      ;
    }

    public function add( $VO ){
			
			$data = $this->app->serializeRegister( $VO, array( ) ) ;
			
			$sql = " insert into tcliente
					(
						" . $data["fields"] . "
					)
					values
					(
						" . $data["values"] . "
          )" ;

      $result = $this->core->execute( $sql ) ;
			
			if( !$result ){
				$this->app->businessError( self::class . "::102", array( "line" => __LINE__ , "file" => __FILE__, "method" => __METHOD__, "sql" => $sql ) ) ;
			}
			
			return $this->core->lastInsertId( ) ;
			
    }

    public function update( $VO ){
			
			$excludeFields = array( "ID" ) ; 
			
			$sql = "update tcliente
					set " . $this->app->prepareUpdateQuery( $VO, $excludeFields ) . " 
					where 1 = 1 
          and request.ID = " . validateString( $VO["ID"] ) ;
          
			$result = $this->core->execute( $sql ) ;
			
			if( !$result ){
				$this->app->businessError( self::class . "::101", array( "line" => __LINE__ , "file" => __FILE__, "method" => __METHOD__ , "sql" => $sql ) ) ;
			}
			
			return true;
			
    }

    public function search( $filter ) {
			
      $sql = $this->sqlSearch( $filter ) ;
			
      $result = $this->core->query( $sql ) ;
			
			if( !$result ){
				$this->app->businessError( self::class . "::102", array( "line" => __LINE__ , "file" => __FILE__, "method" => __METHOD__, "sql" => $sql ) ) ;
			}

			$result->setFetchMode( PDO::FETCH_ASSOC ) ;
			$result = $result->fetchAll( ) ;
			
			return $result ;
    }
    
    private function sqlSearch( $filter ) {
			
      $filterSQL = $this->filter( $filter ) ;
			
      $sql = "select 
        * from tcliente
      " . $filterSQL 
      ;

      return $sql ;
    }

    private function filter( $filter ){
			
			$filterSQL = "
				where 1 = 1
      " ;
      
      if( $filter->getMercado( ) != null && $filter->getMercado( ) != "" && $filter->getMercado( ) != "*" ){
				$filterSQL .= "
        and sMercado = '" . validateString( $filter->getMercado( ) ) . "'
				" ;
      }
    }
  }
?>