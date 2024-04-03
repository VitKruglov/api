<?php
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");

$cnt_query=array();
$cnt_query['cnt']=0;

function getFormData($method,$headers) {
 
    //если в запросе json (для Swagger)
    if(!empty($headers['Content-Type'])){
        if(mb_strtolower($headers['Content-Type'])=='application/json'){
            if($method=='POST'){
                $data=json_decode(file_get_contents('php://input'),TRUE);
                return $data;
            }elseif($method=='PUT'){
                $data=json_decode(str_replace ("\n","",str_replace("\"",'"',file_get_contents('php://input'))),TRUE);
                return $data;
            }
        }
    }

    // GET или POST: данные возвращаем как есть
    if ($method === 'GET') return $_GET;
    if ($method === 'POST') return $_POST;
 
    
    if($method=='PUT' and strpos(mb_strtolower($headers['Content-Type']),'multipart/form-data')!==false){     
        //передача файла
        $data=parse_multipart(file_get_contents('php://input'));

    }elseif($method=='PUT'){ // PUT (для laravel)
        $data = array();
        $exploded = explode('&', file_get_contents('php://input'));
    
        foreach($exploded as $pair) {
            $item = explode('=', $pair);
            if (count($item) == 2) {
                if(substr(urldecode($item[0]),0,5)=='input'){
                    if(!isset($data['input'])) $data['input']=array();
                    $data['input'][substr(urldecode($item[0]),6,strlen(urldecode($item[0]))-7)]=urldecode($item[1]);
                }else
                    $data[urldecode($item[0])] = urldecode($item[1]);
            }
        }
    }elseif($method=='DELETE'){ 
        $data = array();
        $exploded = explode('&', file_get_contents('php://input'));
    
        foreach($exploded as $pair) {
            $item = explode('=', $pair);
            if (count($item) == 2) {
                if(substr(urldecode($item[0]),0,5)=='input'){
                    if(!isset($data['input'])) $data['input']=array();
                    $data['input'][substr(urldecode($item[0]),6,strlen(urldecode($item[0]))-7)]=urldecode($item[1]);
                }else
                    $data[urldecode($item[0])] = urldecode($item[1]);
            }
        }
        if(!isset($data['lrv']))
           $data=json_decode(file_get_contents('php://input'),TRUE);
    }
    else
       $data=json_decode(file_get_contents('php://input'),TRUE);

    return $data;
}

    // Определяем метод запроса
    $method = $_SERVER['REQUEST_METHOD'];
    if($method == 'OPTIONS'){
        header("Allow: OPTIONS, GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Content-Type");
    }
#    echo $method;
 #   print_r ($_SERVER);

    // Разбираем url
    $url = (isset($_GET['q'])) ? $_GET['q'] : '';
    $url = rtrim($url, '/');
    $urls = explode('/', $url);

    // Определяем роутер и url data
    $urlData=array();
    $urlParam=array();
    $token='';

    $ver = $urls[0];
    $router = $urls[1];
    $nameFunction = $urls[2];
    $urlData = array_slice($urls, 3);

    include_once $ver.'/lib/functionApi.php';   

    $headers=apache_request_headers();

    // Получаем данные из тела запроса
    $formData = getFormData($method, $headers);

    foreach($_GET as $key=>$val_get)
        if($key!='q') 
            $urlParam[$key]=$val_get;

    //переносим в urlParam значение pref из formData (например для Http::DELETE, где не передаются данные GET и POST)
    if(!isset($urlParam['pref']) and isset($formData['pref']))
        $urlParam['pref']=$formData['pref'];
    if(!isset($urlParam['session_id']) and isset($formData['session_id']))
        $urlParam['session_id']=$formData['session_id'];        

    //вытаскиваем token из headers     
    if (!empty($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            $token=$matches[1];
        }
    }    

    if($nameFunction=='getParam'){
        list($x1,$x2,$server_name)=explode("/",$headers['Referer']);
        $config=array();
	    $fd = fopen("v1/".$server_name.".conf", "r");
	    if($fd > -1) {
            while (!feof($fd)) {
                $str = fgets($fd, 1000);
                $str = chop($str);
                parse_str($str,$res);
                $config=array_merge_recursive($config,$res);
            }
		    fclose($fd);
        }
    #    $arr_result=array('method' => $method, 'host'=>$headers['Host']);
        echo json_encode(array('data'=>$config),JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    }else{
   #     $arr_result=array('result'=>array('method' => $method, 'input'=>array('token'=>$token, 'urlData'=>$urlData, 'urlParam'=>$urlParam, 'formData'=>$formData, 'post'=>$_POST, 'get'=>$_GET , 'headers'=>$headers)));
    #    echo json_encode($arr_result,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

        // подключаем библиотеку для формирования тегов типа <input>
        include_once $ver.'/lib/input.php';
        // Подключаем файл-роутер и запускаем главную функцию
        include_once $ver.'/objects/' . $router . '.php';
    }
 
?>
