<?php
function mask($string,$mascara){
   $string = str_replace(" ","",$string);
   for($i=0;$i<strlen($string);$i++)
   {
	  $mascara[strpos($mascara,"#")] = $string[$i];
   }
   return $mascara;
}
function distanceCoordinates($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'K') {
	
	$theta = $longitude1 - $longitude2;
	$dist = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) +  cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);

	if ($unit == "K") {
		$distance = ($miles * 1.609344);
	}elseif($unit == "N") {
			$distance = ($miles * 0.8684);
		}else{
			$distance = $miles;
		}
		
	return number_format($distance,3,",",".");
     /*$theta = $longitude1 - $longitude2;
     $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
     $distance = acos($distance);
     $distance = rad2deg($distance);
     $distance = $distance * 60 * 1.1515; switch($unit) {
          case 'Mi': break; case 'Km' : $distance = $distance * 1.609344;
     }
     return (round($distance,2));*/
}

# Gerar cores aleatorias, com base em array pre-definido...
function randColor($arrColors){
	if(count($arrColors)>0){
		$c=array_rand($arrColors);
		$color = $arrColors[$c];
		unset($arrColors[$c]);			
	}else{
		$color = "#".rand(10,99).rand(40,99).rand(70,99);
	}
	return $color;
}
function saveLog($core=null, $credential=0, $session='', $object='', $action=''){
	$return = false;
	if(!is_null($core)){
	
		$core->query('insert into 
						 _log (`CREDENTIAL_ID`,`IP`,`SESSION`,`OBJECT`,`ACTION`,`DATEHOUR`) 
					  values 
						("'.$credential.'","'.getIP().'","'.$session.'","'.$object.'","'.$action.'", NOW() );');
	}
	return $return;
}
function removeAcentos($str) {
	$from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
	$to = "aaaaeeiooouucAAAAEEIOOOUUC";
    $keys = array();
    $values = array();
    preg_match_all('/./u', $from, $keys);
    preg_match_all('/./u', $to, $values);
    $mapping = array_combine($keys[0], $values[0]);
    return strtr($str, $mapping);
}

# Pegar intervalo entre datas (Começo = Segunda / Fim = Domingo)
function rangeWeek($datestr) {
	$datestr = implode("-",array_reverse(explode("/",$datestr)));
	
	date_default_timezone_set(date_default_timezone_get());
	$dt = strtotime($datestr);
	$res['inicio'] = date('N', $dt)==1 ? date('d/m/Y', $dt) : date('d/m/Y', strtotime('last monday', $dt));
	$res['fim'] = date('N', $dt)==7 ? date('d/m/Y', $dt) : date('d/m/Y', strtotime('next sunday', $dt));
	return $res;
}
# Pegar apenas segundas-feira entre datas
function getMondays($start,$end){
	$mondays = array();
	# Range dates...
	$date = implode("-",array_reverse(explode("/",$start)));	
	$start = strtotime($date);
	$date = explode("/",$end);
	$end = mktime(0, 0, 0, $date[1], $date[0], $date[2]);

	$day = $start;
	do {
		$date = date('M d, Y', $day);
		$mondays[] = $date;
	    $day += 7 * 86400;

	} while ($day < $end);
	
	return $mondays;
}
# Quantidade de semanas entre datas..
function datediffInWeeks($date1, $date2){
    $first = DateTime::createFromFormat('d/m/Y', $date1);
    $second = DateTime::createFromFormat('d/m/Y', $date2);
    if($date1 > $date2) return datediffInWeeks($date2, $date1);
    return floor($first->diff($second)->days/7);
}
# Pegar semanas entre datas, com valores base de SEGUNDA e DOMINGO em cada "nó"
function getMondaySundayWeek($start,$end){
	$weeks = array();
	# Range dates...
	$date = implode("-",array_reverse(explode("/",$start)));	
	$start = strtotime($date);
	$date = explode("/",$end);
	$end = mktime(0, 0, 0, $date[1], $date[0], $date[2]);

	$day = $start;
	do {
		$week["monday"] = date('Y-m-d', $day);
		$week["sunday"] = date('Y-m-d', strtotime('next sunday', ($day)));
		
	    $day += 7 * 86400;
		
		$weeks[] = $week;

	} while ($day < $end);
	
	return $weeks;
}
# Adicionar dias em uma data
function addDayIntoDate($date,$days) {
	 $date = explode('-',$date);
     $nextdate = mktime ( 0, 0, 0, $date[1], $date[2] + $days, $date[0] );
     return strftime("%d/%m/%Y", $nextdate);
}
# Subtrair dias em uma data
function subDayIntoDate($date,$days) {
	 $date = explode('-',$date);
     $nextdate = mktime ( 0, 0, 0, $date[1], $date[2] - $days, $date[0] );
     return strftime("%d/%m/%Y", $nextdate);
}

# Gerar senha
function generatePassword( $base = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789" ) {
    $alphabet = $base;
    $pass = array();
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

# Encriptar
function nkCrypt($password){
	$key = '15|24|22|13|26|30|10';
	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $password, MCRYPT_MODE_CBC, md5(md5($key))));
}

# Desencriptar
function nkDecrypt($password){
	$key = '15|24|22|13|26|30|10';
	return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($password), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
}

# Funcao para retornar Array com base na Query [RAIN TPL]
function getData($resultSQL){
	
	$values = array();
	$data = array();
		
	while( $row = $resultSQL ){
	
		# Percorrer CAMPOS do resultado
		foreach($row as $field => $value){
			
			# Validar / Formatar campo DATA
			if( (strtoupper($field)=='DATE') || (strtoupper($field)=='DATA') ){
				$values[$field] = formata_data($value);
			}else{
				$values[$field] = $value;
			}
			
		}	
		# Atribuindo ao Retorno
		$data[] = $values;
		
	}	
	
	return $data;
}

# Aplicar UTF-8 em Array
function array_utf8(array $array) { 
	$convertedArray = array(); 
	foreach($array as $key => $value) { 
	  $convertedArray[$key] = utf8_encode($value); 
	} 
	return $convertedArray; 
} 

//function que formata a data
function formata_data($data){

 $data = explode('-', $data);
 $data = $data[2].'/'.$data[1].'/'.$data[0];
 return $data;

}
//fun磯 que formata a hora
function formata_hora($hora){

 $hora = explode(':', $hora);
 $hora = $hora[0].':'.$hora[1];
 return $hora;

}

function getMounth($data_banco){
  // mostra o nome do m곍
  switch(date("m",strtotime($data_banco))) {
   case "01": $mes = "janeiro";     break;
   case "02": $mes = "fevereiro";   break;
   case "03": $mes = "março";       break;
   case "04": $mes = "abril";       break;
   case "05": $mes = "maio";        break;
   case "06": $mes = "junho";       break;
   case "07": $mes = "julho";       break;
   case "08": $mes = "agosto";      break;
   case "09": $mes = "setembro";    break;
   case "10": $mes = "outubro";     break;
   case "11": $mes = "novembro";    break;
   case "12": $mes = "dezembro";    break; 
 }
 return $mes;
}

function getWeekDay($data_banco){
 switch (date('w',strtotime($data_banco))) {
  case 0: $dia_da_semana = "Domingo"; break;
  case 1: $dia_da_semana = "Segunda-feira"; break;
  case 2: $dia_da_semana = "Terça-feira"; break;
  case 3: $dia_da_semana = "Quarta-feira"; break;
  case 4: $dia_da_semana = "Quinta-feira"; break;
  case 5: $dia_da_semana = "Sexta-feira"; break;
  case 6: $dia_da_semana = "Sábado-feira"; break;
 }
 return $dia_da_semana;
}

function getUFByState( $state ){
	$state = strtolower( $state ) ; 
	
	switch ( $state ) {
		case "acre":                return "ac"; break;
		case "alagoas":             return "al"; break;
		case "amazonas":            return "am"; break;
		case "amapa":               return "ap"; break;
		case "bahia":               return "ba"; break;
		case "ceara":               return "ce"; break;
		case "distrito federal":    return "df"; break;
		case "espirito santo":      return "es"; break;
		case "goias":               return "go"; break;
		case "maranhao":            return "ma"; break;
		case "minas gerais":        return "mg"; break;
		case "mato grosso do sul":  return "ms"; break;
		case "mato grosso":         return "mt"; break;
		case "para":                return "pa"; break;
		case "paraiba":             return "pb"; break;
		case "pernambuco":          return "pe"; break;
		case "piaui":               return "pi"; break;
		case "parana":              return "pr"; break;
		case "rio de janeiro":      return "rj"; break;
		case "rio grande do norte": return "rn"; break;
		case "rondonia":            return "ro"; break;
		case "roraima":             return "rr"; break;
		case "rio grande do sul":   return "rs"; break;
		case "santa catarina":      return "sc"; break;
		case "sergipe":             return "se"; break;
		case "sao paulo":           return "sp"; break;
		case "tocantins":           return "to"; break;		
	}
}

//responsavel por pegar apenas 50 porcento de uma frase: Ex: Appsolute => lute
//utilizacao: Quando nao se sabe exatamente se a frase comeca igualmente, como conversao de endereco 
//em latitude e longitude
function get50PercFromString( $string ){
	$strLenght  = strlen( $string ) ;
	return substr( $string, -4 ) ;
}

function diffDateTime($data){

  $data_atual = mktime();
 
  # separamos as partes da data 
  list($ano,$mes,$dia) = explode("-",$data);
  list($dia,$hora) = explode(" ",$dia);
  list($hora,$min,$seg) = explode(":",$hora);
  
  # transformamos a data do banco em segundos usando a fun磯 mktime()
  $data_banco = mktime($hora,$min,$seg,$mes,$dia,$ano); 

  # subtraimos a data atual menos a data do banco em segundos
  $diferenca = $data_atual - $data_banco; 
 
  $minutos = $diferenca/60; // dividimos os segundos por 60 para transforma-los em minutos
  $horas = $diferenca/3600; // dividimos os segundos por 3600 para transforma-los em horas
  $dias = $diferenca/86400; // dividimos os segundos por 86400 para transforma-los em dias
  
  # Verifica絥s para definir a mensagem a ser mostrada.
	   
  if($minutos < 1){
  
     $diferenca = "h&aacute; menos de um minuto";
	 
  }elseif($minutos > 1 && $horas < 1) {
   	 
		  # plural ou singular de minuto   
		  if(floor($minutos) == 1){ 
		  	 $s = ''; 
		  }else{ 
		 	 $s = 's'; 
		  } 
	      $diferenca = "h&aacute; ".floor($minutos)." minuto".$s;
	 
       }elseif($horas <= 24) { 
	          
			  # plural ou singular de hora   	   		
   			  if(floor($horas) == 1){ 
			  	 $s = ''; 
			  }else{ 
			  	 $s = 's'; 
			  }
			  
			  # plural ou singular de minuto    
   		      if(floor($minutos - floor($horas)*60) != 0){
			  
				  if(floor($minutos - floor($horas)*60) == 1){ 
				  	 $s2 = ''; 
				  }else{ 
				     $s2 = 's'; 
				  } 
    			  
				  $minutos_da_hora = " e ".floor($minutos - floor($horas)*60)." minuto".$s2;
   			  
			  }else{
    			  
				  $minutos_da_hora = '';
   
   			  }
   			  
			  $diferenca = "h&aacute; ".floor($horas)." hora".$s.$minutos_da_hora;
  		 
		 }elseif($dias <= 2){ 
   			    
				 $diferenca = "ontem &agrave;s ".date("H:i");
  			  
			  }elseif($dias <= 8){
   
   					   # mostra o dia da semana 
   					   if(floor($horas) == 1){ 
						  $s = ''; 
					   }else{ 
					  	  $s = 's'; 
					   }
					
   	 				   $diferenca = getWeekDay($data_banco)." &agrave;s ".date("H:i", $data_banco);
					   
   				   }elseif($dias <= 365 && date("Y") == date("Y",$data_banco)) {
				   
	        			    $diferenca = date("d",$data_banco)." de ".getMounth($data_banco)." &agrave;s ".date("H:i", $data_banco);
							
	 	 				}else{
	   						
							$diferenca = date("d/m/Y",$data_banco)." &agrave;s ".date("H:i", $data_banco);
	     				}
  
   return $diferenca;
}

# Trandando strings
function validateString($oldString){
	
	$oldString = str_replace( "\\", "\\\\", $oldString ) ;
	
	# Caracteres HTML / Aspas simples e afins
	$FIND=array("<",">","'" );
	$REPLACE=array("&lt;","&gt;","\'" );
	
	$newString = str_replace($FIND,$REPLACE,$oldString);
	
	return $newString;
}

function getGMT($timestamp){
	//return getLocalTimezone() ;exit;
	# Vamos definir provisoriamente SAO_PAULO como TIMEZONE, depois vemos como vai ficar
	date_default_timezone_set( 'America/Sao_Paulo' );
	
	# Estourando a requisicao
	$DateTime = explode(' ',$timestamp);
	
	#Serializando data/hora
	$date = explode('-',$DateTime[0]);
	$time = explode(':',$DateTime[1]);
	
	# Convertendo para timestamp UNIX
	$tm = mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
	
	$GMT = gmdate("Y-m-d\TH:i:s\Z", $tm);

	return $GMT;
}

function getLocalTimezone()
{
    $iTime = time();
    $arr = localtime($iTime);
    $arr[5] += 1900; 
    $arr[4]++;
    $iTztime = gmmktime($arr[2], $arr[1], $arr[0], $arr[4], $arr[3], $arr[5], $arr[8]);
    $offset = doubleval(($iTztime-$iTime)/(60*60));
    $zonelist = 
    array
    (
        'Kwajalein' => -12.00,
        'Pacific/Midway' => -11.00,
        'Pacific/Honolulu' => -10.00,
        'America/Anchorage' => -9.00,
        'America/Los_Angeles' => -8.00,
        'America/Denver' => -7.00,
        'America/Tegucigalpa' => -6.00,
        'America/New_York' => -5.00,
        'America/Caracas' => -4.30,
        'America/Halifax' => -4.00,
        'America/St_Johns' => -3.30,
        'America/Argentina/Buenos_Aires' => -3.00,
        'America/Sao_Paulo' => -3.00,
        'Atlantic/South_Georgia' => -2.00,
        'Atlantic/Azores' => -1.00,
        'Europe/Dublin' => 0,
        'Europe/Belgrade' => 1.00,
        'Europe/Minsk' => 2.00,
        'Asia/Kuwait' => 3.00,
        'Asia/Tehran' => 3.30,
        'Asia/Muscat' => 4.00,
        'Asia/Yekaterinburg' => 5.00,
        'Asia/Kolkata' => 5.30,
        'Asia/Katmandu' => 5.45,
        'Asia/Dhaka' => 6.00,
        'Asia/Rangoon' => 6.30,
        'Asia/Krasnoyarsk' => 7.00,
        'Asia/Brunei' => 8.00,
        'Asia/Seoul' => 9.00,
        'Australia/Darwin' => 9.30,
        'Australia/Canberra' => 10.00,
        'Asia/Magadan' => 11.00,
        'Pacific/Fiji' => 12.00,
        'Pacific/Tongatapu' => 13.00
    );
    $index = array_keys($zonelist, $offset);
    if(sizeof($index)!=1)
        return false;
    return $index[0];
} 

// Marcar selecionado
function selecionado($dado1,$dado2){
	
	$selected="";
	if($dado1==$dado2){
		$selected="selected";
	}else{
		$array = explode(",",$dado2);
		# Verificar se eh array
		if(in_array($dado1,$array)){
			$selected="selected";
		}
	}
	return $selected;
}

// Marcar checado
function checado($dado1,$dado2){
	
	$checked="";
	if($dado1==$dado2){
		$checked="checked";
	}
	return $checked;
}

// Marcar menu ativo
function menuActive($menu,$req){

	$active="";
	if($menu==$req){
		$active='active';
	}
	return $active;
}

function getDateUTC($date){
	
	#Serializando data
	$date = explode('-',$date);
	
	# Convertendo para timestamp UNIX
	$tm = mktime(0,0,0,$date[1],$date[2],$date[0])*1000;
	//$datetime = $timestamp.' 00:00:00';
	
	return $tm;
}

function translateDay($type,$languages,$day){
	
	# Array week days
	$arrWeek = array();
	$arrWeek["SUNDAY"] = "Domingo";
	$arrWeek["MONDAY"] = "Segunda-feira";
	$arrWeek["TUESDAY"] = "Terça-feira";
	$arrWeek["WEDNESDAY"] = "Quarta-feira";
	$arrWeek["THURSDAY"] = "Quinta-feira";
	$arrWeek["FRIDAY"] = "Sexta-feira";
	$arrWeek["SATURDAY"] = "Sábado";
	
	if($type=="week"){
	
		if($languages=="en-br"){
			
			return $arrWeek[strtoupper($day)];
			exit;
			
		}
		
	}
	
	return "#ERROR: Nothing found!";
	
}

function adjustTime($time,$operation,$value){
	
	# Acertando a hora
	$time = explode(':',$time);
	
	# Verificando operacao
	switch ($operation) {
		case "+" : $hour = $time[0]+$value.':'.$time[1].':'.$time[2];
		case "-" : $hour = $time[0]-$value.':'.$time[1].':'.$time[2];
		default: "Error in opration tag.";
	}
	
	# retornando hora
	return $hour;
}

function getIP(){
   $variables = array('REMOTE_ADDR',
                      'HTTP_X_FORWARDED_FOR',
                      'HTTP_X_FORWARDED',
                      'HTTP_FORWARDED_FOR',
                      'HTTP_FORWARDED',
                      'HTTP_X_COMING_FROM',
                      'HTTP_COMING_FROM',
                      'HTTP_CLIENT_IP');

   $return = '000.000.000.000';

   foreach ($variables as $variable){
   
       if( isset($_SERVER[$variable]) ){
           $return = $_SERVER[$variable];
		   break;
       }
	   
   }
   
   return $return;
}

function removeSpecialChars( $texto ){
  $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" , "!", "@", "#", "$", "¨", "&", "*", "(", ")", "-", "=", "+", "´", "`", "[", "]", "{", "}", "~", "^", ",", "<", ">", ";", ":", "/", "?", "\\", "|", "¹", "²", "³", "£", "¢", "¬", "§", "ª", "º", "°"
                     , "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" );
  $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" , "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""
                     , "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" );
  return str_replace( $array1, $array2, $texto );
}

function removeAspas( $texto ){
  $array1 = array("'" );
  $array2 = array("" );
  return str_replace( $array1, $array2, $texto );
}

# Gerar senha para acesso ao painel
function newPassword($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false){
	$lmin = 'abcdefghijklmnopqrstuvwxyz';
	$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$num = '1234567890';
	$simb = '!@#$%*-';
	$retorno = '';
	$caracteres = '';
	
	$caracteres .= $lmin;
	if ($maiusculas) $caracteres .= $lmai;
	if ($numeros) $caracteres .= $num;
	if ($simbolos) $caracteres .= $simb;
	
	$len = strlen($caracteres);
	for ($n = 1; $n <= $tamanho; $n++) {
		$rand = mt_rand(1, $len);
		$retorno .= $caracteres[$rand-1];
	}
	return $retorno;
}

# Sincronizando usuario NOKADE com Plataformas API
function getSocialUser($queryResult){

	# Instanciando Facebook
	$facebook = new Facebook(array(
	  'appId'  => FACEBOOK_APP_ID,
	  'secret' => FACEBOOK_APP_SECRET,
	));
	
	# Montando array com usuarios do resultado da query
	$i=0;
	while($row = mysql_fetch_array($queryResult,MYSQL_ASSOC)){
		
		# Montando array com os usuarios
		$arrResult[$i]["id"] 	 = $row["USER_ID"];
		$arrResult[$i]["network"] = $row["SOCIAL_NETWORK"];
		
		
		# Verificando campos extras da query // xNOME_CAMPO = Qualquer outro campo extra da query
	    $fields = mysql_num_fields( $queryResult );
	    for ( $f = 0; $f < $fields; $f++ ) {
	    	
			if( (strtoupper(mysql_field_name( $queryResult, $f )) != "USER_ID") && (strtoupper(mysql_field_name( $queryResult, $f )) != "SOCIAL_NETWORK") ){
				$arrResult[$i]["x".strtolower( mysql_field_name( $queryResult, $f ) )] = $row[ strtoupper(mysql_field_name( $queryResult, $f )) ];
			}
			
	    }
		
		$i++;
		
	}
	
	# Montando query p/ pegar usuarios facebook
	$query = "SELECT uid,name,pic_small FROM user WHERE uid=0 "; 
    foreach($arrResult as $i => $value){
	       
		if($arrResult[$i]["network"]=="FACEBOOK"){
			$query .= " OR uid=".$arrResult[$i]["id"]." ";
		}		
			
    }
	
	# Capturando usuarios facebook
	$facebookUsers = $facebook->api(array('method'=>'fql.query','query'=>$query));
	
	# Organizando usuarios / Capturando User Twitter
    foreach($arrResult as $i => $value){

		if($arrResult[$i]["network"]=="FACEBOOK"){
			
			 # Localizando o user
			 foreach($facebookUsers as $f => $value){
			 	
				if( $facebookUsers[$f]["uid"]== $arrResult[$i]["id"]){
					
					$arrResult[$i]["name"] 	 = utf8_decode($facebookUsers[$f]["name"]);
					$arrResult[$i]["picture"] = $facebookUsers[$f]["pic_small"];
					$arrResult[$i]["link"]    = "http://www.facebook.com/profile.php?id=".$facebookUsers[$f]["uid"];
					
				}
				
			 }
			
		}elseif($arrResult[$i]["network"]=="TWITTER"){
		
				# Lib TwitterOAuth
				$local = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] ;
				if( (strstr($local,'/dash')) || (strstr($local,'/site')) ){
				
					require_once('../api/twitter/twitteroauth/twitteroauth.php');
					require_once('../api/twitter/config.php');
					
				}else{
					require_once('../api/twitter/twitteroauth/twitteroauth.php');
					require_once('api/twitter/config.php');
				}
				
				$twitterAPI = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
				$twitter = json_decode(json_encode($twitterAPI->get('users/show/'.$arrResult[$i]["id"])),true);
						
				$arrResult[$i]["name"] 	 = $twitter["screen_name"];
				$arrResult[$i]["picture"] = $twitter["profile_image_url"];	
				$arrResult[$i]["link"]    = "http://www.twitter.com/".$twitter["screen_name"];
					
			}elseif($arrResult[$i]["network"]=="NOKADE"){
				
					# Caputando dados principais nokade
					$result = mysql_query('select USERNAME,LOGO from _profile where ID ='.$arrResult[$i]["id"]);
					$nokade = mysql_fetch_array($result,MYSQL_ASSOC);
						
					$arrResult[$i]["name"] 	 = $nokade["USERNAME"];
					$arrResult[$i]["picture"] = $nokade["LOGO"];
					$arrResult[$i]["link"]    = "http://www.nokade.com/".$nokade["USERNAME"];
					
				}
		
    }
	
	return $arrResult;
}

# Carregando feed
function loadFeed($queryResult){
	
	# Montando array com feeds do resultado da query
	$i=0;
	while($row = mysql_fetch_array($queryResult,MYSQL_ASSOC)){
	
		# Username em questao 
		if( isset($_SESSION["USERNAME"]) && ($_SESSION["USERNAME"]!="")){
			$profileUsername = $_SESSION["USERNAME"];
		}else{
			$profileUsername = $_GET["site"];
		}
	
		# Usuario
		if($row["SOCIAL_NETWORK"]=="FACEBOOK"){
		
			$userName = '<a href="http://www.facebook.com/profile.php?id='.$row["USER_ID"].'" title="Em '.formata_data($row["DATE"]).' &agrave;s '.formata_hora($row["HOUR"]).'" target="_blank">'.utf8_encode($row["USER"]).'</a>';
			
		}elseif( $row["SOCIAL_NETWORK"]=="TWITTER" ){
		
				$userName = '<a href="http://www.'.strtolower($row["SOCIAL_NETWORK"]).'.com/'.$row["USER"].'"  title="Em '.formata_data($row["DATE"]).' &agrave;s '.formata_hora($row["HOUR"]).'" target="_blank">'.utf8_encode($row["USER"]).'</a>';
		
			 }elseif( ($row["SOCIAL_NETWORK"]=="NOKADE") && ($row["USER"]=="") ){
							
					 # Caputando dados principais nokade
					 $result = mysql_query('select USERNAME from _profile where ID ='.$row["USER_ID"]);
					 $nokade = mysql_fetch_array($result,MYSQL_ASSOC);	
					 
					 $userName = '<a href="http://www.nokade.com/'.$nokade["USERNAME"].'" title="Em '.formata_data($row["DATE"]).' &agrave;s '.formata_hora($row["HOUR"]).'" target="_blank">'.$nokade["USERNAME"].'</a>';
								
			 	  }elseif( ($row["SOCIAL_NETWORK"]=="NOKADE") && ($row["USER"]!="") ){

				  		$userName = '<a href="http://www.nokade.com/'.$row["USER"].'" title="Em '.formata_data($row["DATE"]).' &agrave;s '.formata_hora($row["HOUR"]).'" target="_blank">'.utf8_encode($row["USER"]).'</a>';
					 
				 	 }
				  
		# Objeto que sofreu comentario ou "like"
		if($row["OBJECT_TYPE"]!="NULL"){
		
			if($row["OBJECT_TYPE"]=="POST"){
				
				$object = 'um post';
				
			}elseif($row["OBJECT_TYPE"]=="PAGE"){
			
			
					$object = ' <a target="_blank" href="http://www.nokade.com/'.$profileUsername.'/'.str_replace(" ","",removeSpecialChars(utf8_encode($row["OBJECT_NAME"]))).'">'.utf8_encode($row["OBJECT_NAME"]).'</a>';
			
				 }elseif($row["OBJECT_TYPE"]=="EVENT"){
									
						$object = ' no evento <a href="http://www.nokade.com/'.$profileUsername.'/'.$row["OBJECT_ID"].'" target="_blank">'.utf8_encode($row["OBJECT_NAME"]).'</a>';
					
				 	  }
				  
		}
		
		# Texto
		if($row["TYPE"]=="PARTNER"){
		
			$text = $userName.' adicionou como parceiro';
			
		}elseif($row["TYPE"]=="MESSAGE"){
		
				$text = $userName.' enviou uma <a href="http://www.nokade.com/dash/message.php?id='.$row["TYPE_ID"].'" title="Enviada em '.formata_data($row["DATE"]).' &agrave;s '.formata_hora($row["HOUR"]).'">mensagem</a>';
			
			}elseif($row["TYPE"]=="FAN"){
			
					$text = $userName.' se tornou fã';
					
				}elseif($row["TYPE"]=="COMMENT"){
						
						if($row["OBJECT_TYPE"]=="PAGE"){
							
							$text = $userName.' comentou em '.$object;
						
						}else{
						
							$text = $userName.' comentou '.$object;
							
						}
					
					}elseif($row["TYPE"]=="PARTICIPANT"){		
						
							$text = $userName.' participaando '.$object;
						
						 }else{
						
							$text = $userName.' curtiu '.$object;
						
						 }
		
		$arrResult[$i]["class"] = strtolower($row["TYPE"]);
		$arrResult[$i]["text"]  = utf8_encode($text);
		
		$i++;
	}
	
	return $arrResult;

}

# Calculando diferencas de tempo
function diffDate($d1, $d2){

	// Define os valores a serem usados
	$data_inicial = '23/03/2009';
	$data_final = '04/11/2009';

	# Explodindo data inicial / final
	$d1 = explode('-', $d1);
	$data_inicial = mktime(0, 0, 0, $d1[1], $d1[2], $d1[0]);

	//return $d1[1].'-'.$d1[2].'-'.$d1[0];exit;

	$d2 = explode('-', $d2);
	$data_final = mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]);
	
	# Calcula a diferença de segundos entre as duas datas
	$diff = $data_final - $data_inicial;
	
	# Calcula a diferença de dias
	$dias = (int)floor( $diff / (60 * 60 * 24));
	
	return $dias;
	
}

# Curl or File Get
function getURL($url, $CURL = FALSE, $jsonDECODE = FALSE){

	if($CURL==TRUE){
		
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    	$data = curl_exec($ch);
    	curl_close($ch);
	
	}else{
		$data = file_get_contents($url,TRUE);
	}
	
	if($jsonDECODE==TRUE){
		
		return json_decode($data,TRUE);
	
	}else{
		
    	return $data;
		
	}
}

# Gerar URL com hyperlink, a partr de uma string
function generateLink($string){

   # Filtro de expressão regular
   $regURL = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

   # Verificando URL no texto
   if(preg_match($regURL, $string, $url)) {

      # Retornando texto com hyperlink / com texto encurtado
	  $find = array('http://','www.');
	  $replace = array('','');
	  $urlText = str_replace($find,$replace,$url[0]);
	  
   	  $string = preg_replace($regURL, '<a href="'.$url[0].'" target="_blank">'.$urlText.'</a>', $string);

   }else{

   	  # Texto inicial
      $string = $string;

   }
   
   return $string;

}

# Verificar se possui link
function getLink($string, $type = 'text' ){

  /**
  *  Filtro de expressão regular
  *  Vendo o Tipo, por padrão é TEXT [pega a url de uma string]
  **/
   if( strtoupper($type) == 'IMG' ){
   	  $regURL ="!http://[^?#]+\.(?:jpe?g|png|gif)!Ui";
   }else{
   	  $regURL = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
   }
   
   $returnURL = "";
   
   # Verificando URL no texto
   if(preg_match($regURL, $string, $url)) {
	  
   	  $string = $url[0];

   }
   
   return strip_tags($string);

}

function Randomizar($iv_len)
{
    $iv = '';
    while ($iv_len-- > 0) {
        $iv .= chr(mt_rand() & 0xff);
    }
    return $iv;
}

function Encriptar($texto, $senha, $iv_len = 16){
    $texto .= "\x13";
    $n = strlen($texto);
    if ($n % 16) $texto .= str_repeat("\0", 16 - ($n % 16));
    $i = 0;
    $Enc_Texto = Randomizar($iv_len);
    $iv = substr($senha ^ $Enc_Texto, 0, 512);
    while ($i < $n) {
        $Bloco = substr($texto, $i, 16) ^ pack('H*', md5($iv));
        $Enc_Texto .= $Bloco;
        $iv = substr($Bloco . $iv, 0, 512) ^ $senha;
        $i += 16;
    }
    return base64_encode($Enc_Texto);
}

function Desencriptar($Enc_Texto, $senha, $iv_len = 16){
    $Enc_Texto = base64_decode($Enc_Texto);
    $n = strlen($Enc_Texto);
    $i = $iv_len;
    $texto = '';
    $iv = substr($senha ^ substr($Enc_Texto, 0, $iv_len), 0, 512);
    while ($i < $n) {
        $Bloco = substr($Enc_Texto, $i, 16);
        $texto .= $Bloco ^ pack('H*', md5($iv));
        $iv = substr($Bloco . $iv, 0, 512) ^ $senha;
        $i += 16;
    }
    return preg_replace('/\\x13\\x00*$/', '', $texto);
}

function appsolute_error_log( $msg ){
	date_default_timezone_set('America/Sao_Paulo');
	$date = date('Y-m-d H:i:s.u');	
	error_log( $date.":: ".$msg."\n", 3, "_log.log" ) ;
}

function appsolute_gettime( ){
	date_default_timezone_set('America/Sao_Paulo');
	$date = date('Y-m-d H:i:s.u');	
	return $date ;
}

function appsolute_calctimeSQL( $sql, $date1, $date2 ){
	$dateDiff = strtotime( $date2 ) - strtotime( $date1 ) ;
	$dateDiff = $dateDiff * 1000 ;
	error_log( appsolute_gettime().":: TOTAL SQL TIME=".$dateDiff."\n".$sql."\n", 3, "_log.log" ) ;
	
	return $dateDiff ;
}

function removeDecimalNumber( $number ){
	$number_array = explode( ",", $number ) ;
	return $number_array[0] ;
}
?>