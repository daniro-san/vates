<?php
  require_once __DIR__ . '/../../lib/config.php';
  require_once __DIR__ . '/../../lib/appsolute.php';

  require_once __DIR__ . '/../../core/Core.class.php';

  require_once __DIR__ . '/../../business/app/Appsolute.class.php';
  require_once __DIR__ . '/../../business/app/AppsoluteDate.class.php';

  require_once __DIR__ . '/../../business/cliente/Cliente.class.php';
  require_once __DIR__ . '/../../business/cliente/ClienteFilter.class.php';


  $core = new Core( ) ;
  $core->beginTransaction( ) ;
  $app  = new Appsolute( $core ) ;

  $cliente        = new Cliente( $core, $app       ) ;
  $filter         = new ClienteFilter( $core, $app ) ;

  if(
    isset( $_REQUEST["type"]            )             &&
    ( $_REQUEST["type"] === Appsolute::SERVICE_TYPE ) &&
    isset( $_REQUEST['filter']     )
  ){

    $filterArray = json_decode( $_REQUEST['filter'], true ) ;

    $filter->setMercado( $filterArray['MERCADO'] ) ;

    $rsSearch  = $cliente->search(  $filter ) ;
    
    $json = processResult( $rsSearch ) ;
    
    $app->response( $json ) ;
  } else {
    $app->businessError( "Invalid params", $_REQUEST ) ;
  }

  function processResult( $rsSearch ){

		global $appPage ;

		$json['data']    = $rsSearch  ;

		$json['status']  = Appsolute::CODE_SERVICE_SUCCESS      ;
    $json['message'] = 'Service Success!'                   ;
		
    return $json ;
  }
?>