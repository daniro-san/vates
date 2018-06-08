<?php

	Class AppHTTP {
		
		private $methods = array(
							'POST', // Create
							'GET', // Read
							'PUT', // Update/Replace
							'PATCH', // Update/Modify
							'DELETE' // Delete
							);
							
		function __construct(){
		}
		
		public function send( $url, $data, $method ){

			if(!in_array($method, $this->methods)){
				return "Invalid HTTP verb";
			}

			// use key 'http' even if you send the request to https://...
		    $options = array(
		        'http' => array(
		            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		            'method'  => $method,
		            'content' => http_build_query($data)
		        )
		    );

		    $context  = stream_context_create($options) ;

		    $result = @file_get_contents($url, false, $context) ;
			
		    return $result ;
		}
		
	}
	
?>