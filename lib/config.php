<?php
  ini_set("date.timezone", "America/Sao_Paulo");

  /** Client info	*/
  define("CLIENT_NAME", "Appfluxo");
  $shortcut = explode("/",$_SERVER["REQUEST_URI"]);
  define( "CLIENT_SHORTCUT", $shortcut[ 1 ] ) ; # Responsável por formar a URL do sistema... http://routy.com.br/CLIENT_SHORTCUT

  // // https disabled
  // if (! isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off' ) {
  //     $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  //     header("Location: $redirect_url");
  //     exit();
  // }

  /** System info	*/
  $domain = (!empty($_SERVER['HTTPS'])) ?
                "https://".$_SERVER['SERVER_NAME']:
                "http://".$_SERVER['SERVER_NAME'];

  $domain .= "/" . CLIENT_SHORTCUT;
  define( "SYSTEM_NAME", CLIENT_NAME                           ) ;
  // define( "SYSTEM_CLIENT", "Grupo Hércules"                    ) ;
  define( "SYSTEM_DOMAIN", $domain                             ) ;
  define( "SYSTEM_LOGO", $domain."/admin/images/thumbnail.png" ) ;
  define( "SYSTEM_COLOR", "default"                            ) ;

  # Sessao...
  define("SYSTEM_SESSION_LIMIT", 4 * (60 * 60) ); # horas (minutos x segundos)
  # Validar nome de sessão única para o cliente (SUBPASTA)
  $client_form = explode("/",$_SERVER["REQUEST_URI"]);
  $client_form = "$_SERVER[HTTP_HOST]".($client_form[1]);
  $client_form = str_replace(array("/",".","admin"),array("","",""),$client_form);
  $SYSTEM_SESSION_NAME = SYSTEM_NAME."-".$client_form;
  define("SYSTEM_SESSION_NAME", $SYSTEM_SESSION_NAME );

  define("SYSTEM_MAPQUEST_KEY","") ;

  define("SYSTEM_MAIL_HOST","");
  define("SYSTEM_MAIL_NAME", SYSTEM_NAME);
  define("SYSTEM_MAIL_USERNAME","");
  define("SYSTEM_MAIL_PASSWORD","");
  define("SYSTEM_MAIL_PORT","");
  define("SYSTEM_MAIL_REPLY","");
  define("SYSTEM_MAIL_LOGO", $domain."/admin/images/thumbnail.png");

  define( "DANGER",  "danger"  ) ;
  define( "WARNING", "warning" ) ;
  define( "SUCCESS", "success" ) ;
  define( "INFO",    "info"    ) ;
  define( "PRIMARY", "primary" ) ;
  define( "DEFAULT", "default" ) ;

?>