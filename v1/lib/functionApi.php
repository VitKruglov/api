<?php
function encode($unencoded,$key){//Шифруем
    $newstr='';
    $string=base64_encode($unencoded);//Переводим в base64
    
    $arr=array();//Это массив
    $x=0;
    while ($x++< strlen($string)) {//Цикл
        $arr[$x-1] = md5(md5($key.$string[$x-1]).$key);//Почти чистый md5
        $newstr = $newstr.$arr[$x-1][3].$arr[$x-1][6].$arr[$x-1][1].$arr[$x-1][2];//Склеиваем символы
    }
    return $newstr;//Вертаем строку
}

function change_phone($phone=null){
	$phone=preg_replace('/[^0-9]/', "", $phone);
	if($phone[0]==8) $phone='7'.substr($phone,1);
	elseif($phone[0]==9) $phone='7'.$phone;
	elseif($phone[0]!=7) $phone='7'.$phone;
	$phone=substr($phone,0,11);
	$phone=str_pad($phone, 11, "0", STR_PAD_RIGHT);
	return $phone;
}

function change_int($str=null, $length=null){
	$str=preg_replace('/[^0-9]/', "", $str);
	$str=substr($str,0,$length);
	return $str;
}

function findObj($level,$obj){
	global $arr_obj;
    foreach($obj as $key=>$val){
        if(substr($key,0,3)=='obj'){
            if(!isset($arr_obj[$key]) or $level<$arr_obj[$key])
                $arr_obj[$key]=$level;
            findObj($level+1,$val);
        }
    }
}

function delObj($level,$obj){
	global $arr_obj;
    foreach($obj as $key=>$val){
        if(substr($key,0,3)=='obj'){
            if(!isset($arr_obj[$key]))
                unset($obj[$key]);
            else{
				unset($arr_obj[$key]);
				$obj[$key]=delObj($level+1,$val);
			}
        }
    }
	return $obj;
}

function getApi3($db=NULL,$pref=NULL, $objName=NULL, $nameFunction=NULL, $search_name=null, $search_val=null, $without=null, $type=null, $domain=null, $this_arr=null, $token=NULL, $session_id=NULL){
    $method="GET";

	$call_from_api="Yes";

    if(!is_null($objName) and !is_null($nameFunction)){
		if(!is_array($search_name) and !is_array($search_val))
			if(isset($this_arr[$nameFunction][$search_name][$search_val]))
				return $this_arr;
			
        $path=$_SERVER['DOCUMENT_ROOT']."/api/".$GLOBALS['ver']."/objects/";
        include_once $path . $objName . '.php';

		$$objName = new $objName($db);

		$urlParam['pref']=$pref;
		if(is_array($search_name)){
			foreach($search_name as $key_search=>$search_val)
				$urlParam['search'][$key_search]=$search_val;
		}else
			$urlParam['search'][$search_name]=$search_val;

		$$objName->method = $method;
		$$objName->urlParam = $urlParam;  //данные из адресной строки после ?
		$$objName->session_id = $session_id;
		$$objName->call_from_api = $call_from_api;
		$$objName->without = $without;
		if(!is_null($token))
			$$objName->token = $token;
		if(!is_null($domain))
			$$objName->domain = $domain;
		if(!is_null($session_id))
			$$objName->session_id = $session_id;
		//добавляем глобальные массивы данных
		if(!is_null($this_arr))
			$$objName->arr=$this_arr;


		$$objName->$nameFunction();       //вызываем нужную функцию класса по имени из URL

		//разбираем ответ и вычлиняем sort и search и colspan на будущее
		$res=$$objName->result;
		
		if($type=='array'){			//если нужно вернуть массив
			if(is_array($res))
				$$objName->arr[$nameFunction][$search_name][$search_val]=$res;
			elseif(is_null($res))
				$$objName->arr[$nameFunction][$search_name][$search_val]=array();
		}elseif(is_array($res)){
			if(count($res)>1)
				$$objName->arr[$nameFunction][$search_name][$search_val]=$res;
			else
				$$objName->arr[$nameFunction][$search_name][$search_val]=$res[0];
		}
		return $$objName->arr;
    }else{
        return NULL;
    }
}

function getApi2($db=NULL,$pref=NULL, $objName=NULL, $nameFunction=NULL, $search_name=null, $search_val=null, $without=null, $type=null, $domain=null, $this_arr=null){
    $method="GET";

	$call_from_api="Yes";

    if(!is_null($objName) and !is_null($nameFunction)){
		if(!is_array($search_name) and !is_array($search_val))
			if(isset($this_arr[$nameFunction][$search_name][$search_val]))
				return $this_arr[$nameFunction][$search_name][$search_val];
			
        $path=$_SERVER['DOCUMENT_ROOT']."/api/".$GLOBALS['ver']."/objects/";
        include_once $path . $objName . '.php';

		$$objName = new $objName($db);

		$urlParam['pref']=$pref;
		if(is_array($search_name)){
			foreach($search_name as $key_search=>$search_val)
				$urlParam['search'][$key_search]=$search_val;
		}else
			$urlParam['search'][$search_name]=$search_val;

		$$objName->method = $method;
		$$objName->urlParam = $urlParam;  //данные из адресной строки после ?
		$$objName->session_id = $session_id;
		$$objName->call_from_api = $call_from_api;
		$$objName->without = $without;
		if(!is_null($domain))
			$$objName->domain = $domain;
		//добавляем глобальные массивы данных
		if(!is_null($this_arr))
			$$objName->arr=$this_arr;


		$$objName->$nameFunction();       //вызываем нужную функцию класса по имени из URL

		//разбираем ответ и вычлиняем sort и search и colspan на будущее
		$res=$$objName->result;

		if($type=='array'){			//если нужно вернуть массив
			if(is_array($res))
				return $res;
			elseif(is_null($res))
				return array();
		}elseif(is_array($res)){
			if(count($res)>1)
				return $res;
			else
				return $res[0];
		}
    }else{
        return NULL;
    }
}

function getApi($token=NULL, $url, $function=NULL){
	//вызов API методов через CURL- не используется но работает

	$json = json_encode ($data, JSON_UNESCAPED_UNICODE);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($json),
		'Authorization: Bearer '.$token)
	 );
	curl_setopt($curl, CURLOPT_POSTFIELDS, $json); 
	curl_setopt($curl, CURLOPT_URL, $url.$function);
	$r=trim(curl_exec($curl));
	$return = json_decode($r, TRUE);

    return $return['data'][0];
    
}

function parse_multipart($data=null){
	if(!is_null($data)){
		$result=array();
		list($lable)=explode(chr(13).chr(10),$data);
		$arr=explode($lable,$data);
		foreach($arr as $k=>$val){
			$a=explode(chr(13).chr(10),$val);
			if(isset($a[1]) and isset($a[4])){
				$name=$a[1];
				$body=$a[4];
				$body=str_replace(chr(13).chr(10).$name.chr(13).chr(10).chr(13).chr(10),"",$val);
			
				$name=str_replace("Content-Disposition: form-data; name=\"","",$name);
				$name=substr($name,0,strlen($name)-1);
			#	for($i=0;$i<strlen($body);$i++)
			#		$result[$k]=$result[$k].ord($body[$i])." ";
				$result[$name]=str_replace($lable."--","", substr($body,0,strlen($body)-2));
			}
		}
		return $result;
	}
}
?>
