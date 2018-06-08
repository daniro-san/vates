<?php

  Class ClienteFilter {
    private $mercado = null;

    public function __construct( ){
    }

    public function setMercado( $mercado ){
			$this->mercado = $mercado ;
		}
		public function getMercado( ){
			return $this->mercado ;
    }

    public function toArray( ){
      return get_object_vars( $this ) ;
    }
  }
?>