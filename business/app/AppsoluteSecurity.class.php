<?php

class AppsoluteSecurity{
	
	public function appProtectionField( $field ){
		
		return( addslashes( $field ) ) ;
		
	}
	
	public function appProtectionJSON( $jsonString ){
		
		$arrayStripSlash = json_decode( stripslashes( $jsonString ), TRUE ) ;
		
		$this->addScape( $arrayStripSlash ) ;
		
		return $arrayStripSlash ;		
	}
	
	private function addScape( &$array ){
		
		if( is_array( $array ) ){
		
			foreach( $array as $key => $value ){
				
				if( is_array( $value ) ){				
					$this->addScape( $array[$key] ) ;
				}else{
					$array[$key] = addslashes( $value ) ;
				}
			}
		}
	}
}
?>