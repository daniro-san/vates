<?php
  require_once __DIR__ . '/../../lib/config.php';
  require_once __DIR__ . '/../../lib/appsolute.php';

  require_once __DIR__ . '/../../core/Core.class.php';

  require_once __DIR__ . '/../../business/app/Appsolute.class.php';
  require_once __DIR__ . '/../../business/app/AppsoluteDate.class.php';

  require_once __DIR__ . '/../../business/cliente/Cliente.class.php';


  $core = new Core( ) ;
  $core->beginTransaction( ) ;
  $app  = new Appsolute( $core ) ;

  $cliente = new Cliente( $core, $app );

  if(
    isset( $_REQUEST["type"]            )             &&
    ( $_REQUEST["type"] === Appsolute::SERVICE_TYPE ) &&
    isset( $_REQUEST["cliente"]            )
  ){

    $cliente = $_REQUEST["cliente"]

    // validate( $clienteVO ) ;

    $rsClienteDelete = $cliente->delete( $cliente ) ;

    $json["request"]      = array( "ID"         => $rsClienteDelete ) ;
    $json["status"]       = Appsolute::CODE_SERVICE_SUCCESS        ;
    $json["message"]      = "Service Success!"                     ;

    $app->response( $json ) ;
  } else {
    $app->businessError( "Invalid params", $_REQUEST ) ;
  }
?>