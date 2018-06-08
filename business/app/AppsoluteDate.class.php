<?php

class AppsoluteDate{ // OS
	
	private $core   = null ;
	
	const TYPE_DAY   = "D" ;
	const TYPE_MONTH = "M" ;
	const TYPE_YEAR  = "Y" ;		
	
	public function __construct(){		
		$this->core = new Core( ) ;
  }
  
  public function checkHoliday( $date ){

    $sql = "
        select
              1
        from dual
        where exists(
          select
                1
          from feriado
          where feriado.DATA = '" . validateString( $date ) . "'
        )
    " ;
    
    $result = $this->core->query_first( $sql ) ;

    if( $this->core->getError( ) != "" ){
      $this->app->businessError( self::class . "|" . __FILE__ . "|" . __LINE__ . "|" . __FUNCTION__  ) ;
    }

    return !!$result ;

  }
	
	public function addDate( $date, $quantity, $type ){
		
		switch( $type ){
			case AppsoluteDate::TYPE_DAY:
				$typeSQL = "DAY" ;			
				break ;
			
			case AppsoluteDate::TYPE_MONTH:
				$typeSQL = "MONTH" ;
				break ;
			
			case AppsoluteDate::TYPE_YEAR:				
				$typeSQL = "YEAR" ;
				break ;
			
			default:
				$this->reportError( "no value valid to type date" )  ;				
		}
		
		if( $date = "now()" ){
			$sql = "select DATE_ADD( now(), INTERVAL ".validateString( $quantity )." ".validateString( $typeSQL )." ) _DATE " ;
		}else{
			$sql = "select DATE_ADD('".validateString( $date )."', INTERVAL ".validateString( $quantity )." ".validateString( $typeSQL )." ) _DATE " ;
		}
		
		$result = $this->core->query_first( $sql ) ;
				
		return $result["_DATE"] ;
	}
	
	public function reportError( $detail ){
		
		$json["status"]=50 ;
		$json["message"]="AppsoluteDate Error. ".$detail ;
		
		echo json_encode($json);
		exit;		
	}
	
	public function getNow( $format ){
		
		$sql    = "select DATE_FORMAT( now( ), '".$format."' ) _DATE " ;
		$result = $this->core->query_first( $sql ) ;
		
		return $result["_DATE"] ;
	}

    public function addMonth( $month, $quantity ){

        $month .= "-01" ;

        $rsDate = $this->addDate( $month, $quantity, AppsoluteDate::TYPE_MONTH ) ;

        return substr( $rsDate, 0, 7 ) ;

    }

    public function getFirstDateTime( $month ){

        $firstDay = $month."-01" ;

        return $firstDay." 00:00:00" ;

    }

    public function getLastDateTime( $month ){

        $array   = explode( '-', $month ) ;
        $lastDay = date( "t", mktime( 0, 0, 0, floatval( $array[1] ), '01', floatval( $array[0] ) ) ) ;

        return $month."-".$lastDay." 23:59:59" ;

    }


}
?>