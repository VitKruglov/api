<?php
//--------------------------------------------------------------------------------------------//
//                                                                                            //
//      библиотека проверки аутентификации, авторизации и формирования тегов <input>          //
//                                                                                            //
//--------------------------------------------------------------------------------------------//
$root = GetEnv('LIB');
if(!$root) $root="/opt/crom/www/lib";

Include("$root/db.php");	//echo "db.php\n";
Include("$root/system.php");	//echo "system.php\n";

//обязательные параметры
if($urlParam['lrv']) 
    $lrv=$urlParam['lrv'];
elseif($postParam['lrv']) 
    $lrv=$postParam['lrv'];
elseif($_POST['lrv']) 
    $lrv=$_POST['lrv'];
elseif($formData['lrv']) 
    $lrv=$formData['lrv'];

if($urlParam['pref']) 
    $pref=$urlParam['pref'];
elseif($postParam['pref']) 
    $pref=$postParam['pref'];
elseif($_POST['pref']) 
    $pref=$_POST['pref'];
elseif($formData['pref']) 
    $pref=$formData['pref'];

if($urlParam['session_id']) 
    $session_id=$urlParam['session_id'];
elseif($postParam['session_id']) 
    $session_id=$postParam['session_id'];    
elseif($_POST['session_id']) 
    $session_id=$_POST['session_id'];    
elseif($formData['session_id']) 
    $session_id=$formData['session_id'];    

if($urlParam['domain']) 
    $domain=$urlParam['domain'];
elseif($postParam['domain']) 
    $domain=$postParam['domain'];    
elseif($_POST['domain']) 
    $domain=$_POST['domain'];    
elseif($formData['domain']) 
    $domain=$formData['domain'];  

#if($domain=='outsourcing.complat.ru' and !is_null($pref)) $domain='ma-test-1.rdtn.ru';

if(!$token){
    if($urlParam['token']) 
        $token=$urlParam['token'];
    elseif($postParam['token']) 
        $token=$postParam['token']; 
    elseif($_POST['token']) 
        $token=$_POST['token']; 
    elseif($formData['token']) 
        $token=$formData['token'];         
}

//подключаем необходимую БД
$config = Config($tmpl, "$root/weblib.conf");
$db = DBConnect($tmpl, $config, $pref);
$db_main = DBConnect($tmpl, $config);

DBExecuteNew($db, "SET character_set_server = 'utf8mb4'");


//проверка токена
if(is_null($pref)){ //для админской панели с любого url
    $s="111";
    list($auth_token,$timezone,$id_unit,$session_timeout)=DBQueryNew($db_main, "SELECT 1, p.timezone, p.id_unit, p.session_timeout FROM persons as p WHERE p.id=$session_id and p.remember_token='".addslashes($token)."'");
    $pref_old=$db_main;
}else{            //для АК доступ только с его URL
    if($token[0]=="'" and substr($token,-1,1)=="'")
        $query="SELECT 1, p.timezone, p.id_unit, p.session_timeout FROM ".$pref."persons as p WHERE p.id=$session_id and p.remember_token=$token";
    else
        $query="SELECT 1, p.timezone, p.id_unit, p.session_timeout FROM ".$pref."persons as p WHERE p.id=$session_id and p.remember_token='$token'";

    list($auth_token,$timezone,$id_unit,$session_timeout)=DBQueryNew($db, $query);
    if($auth_token==1) 
        list($auth_token)=DBQueryNew($db_main, "SELECT 1 FROM owner as o WHERE o.domain='$domain'");
}

//если токен не подходит Заканчиваем
if($method=='OPTIONS'){

}elseif(!$auth_token and $nameFunction!='authLogin' and $nameFunction!='authRefresh'){
    http_response_code(401);
    $arr_result=array('result'=>array('result'=>'Неавторизованный запрос', 'error'=>$query, 'token'=>$token, 'session_id'=>$session_id, 'pref'=>$pref, 'domain'=>$domain, 'pref_old'=>$pref_old));
    echo json_encode($arr_result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    exit();
}elseif($session_timeout<time() and $nameFunction!='authLogin' and $nameFunction!='authRefresh'){
    http_response_code(500);
    $arr_result=array('result'=>array('Закончилось время действия access token', 'error'=>500));

    echo json_encode($arr_result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    exit();
}


//проверяем права пользователя
$query="SELECT type FROM ".$pref."permis_person WHERE id_person=$session_id and api_name='".$router."/".$nameFunction."' UNION
SELECT type FROM ".$pref."permis_person WHERE id_person=$session_id and api_name='".$router."/*' UNION
SELECT type FROM ".$pref."permis_person WHERE id_person=$session_id and api_name='*/*' UNION
SELECT type FROM ".$pref."permis_unit WHERE id_unit=$id_unit and api_name='*/*' UNION 
SELECT type FROM ".$pref."permis_unit WHERE id_unit=$id_unit and api_name='".$router."/*' UNION 
SELECT type FROM ".$pref."permis_unit WHERE id_unit=$id_unit and api_name='".$router."/".$nameFunction."' LIMIT 1";

list($permis) = DBQueryNew($db, $query);

if($permis<1 and $method!='OPTIONS' and $nameFunction!='authLogin' and $nameFunction!='authRefresh'){
    http_response_code(403);
    $arr_result=array('result'=>'Доступ к ресурсу запрещен', 'error'=>403, 'token'=>$token, 'session_id'=>$session_id, 'pref'=>$pref, 'domain'=>$domain, 'query'=>$query);
    echo json_encode($arr_result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    exit();
}


if($timezone>0)
	DBExecuteNew($db, "SET time_zone = '+".str_pad($timezone, 2, "0", STR_PAD_LEFT).":00'");
elseif($timezone<0)
	DBExecute($db, "SET time_zone = '-".str_pad($timezone*(-1), 2, "0", STR_PAD_LEFT).":00'");


//--------------------------- отключаем протоколирование MySql ошибок - на этапе разработки -------------------//
mysqli_report(MYSQLI_REPORT_OFF);


class Input
{
    // подключение к базе данных и таблице "products"
    private $conn;

    // свойства объекта
    public $result;         //результат выполения Mysql запроса

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function inputText($name,$size)
    {
        $s="<input class=tR type=text name=\"$name\" id=\"$name\" size=$size  value=\"\"></input>";
        return $s;
    }
    function inputSelect($name,$width,$query)
    {
        $result=DBFetchNew($this->conn, $query);

        if(count($result)>0){
            $s="<select class=tR name=\"$name\" style=\"width:".$width."px;\" onChange=\"this.form.submit();\">";
            $s.="<option value=\"\">Все</option>";
            foreach($result as $key=>$val){
                list($id,$name)=$val;
                $s.="<option value=\"$id\">$name</option>";
            }
            $s.="</select>";
        }
        
        return $s;
    }

}

?>
