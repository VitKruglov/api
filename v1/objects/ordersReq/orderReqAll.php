<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о требованиях к ЛП   format2    //
//                                                             //
//-------------------------------------------------------------//

$name_script="orders_req.dhtml";
$name_script_info="order_req_info.dhtml";


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['id_rate']))
        $id_rate=$data['id_rate'];    
    if(isset($data['sort']))
        $sort=$data['sort'];

    $session_id=$data['session_id'];
}

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    2=>"<a href=\"".$name_script."?sort=id_request\" class=\"upmenu\">Заявка</a>",
    3=>"<a href=\"".$name_script."?sort=gender\" class=\"upmenu\">Пол</a>",
    4=>"<a href=\"".$name_script."?sort=age_after\" class=\"upmenu\">Возраст от</a>",
    5=>"<a href=\"".$name_script."?sort=age_before\" class=\"upmenu\">Возраст до</a>",
    6=>"<a href=\"".$name_script."?sort=national\" class=\"upmenu\">Гражданство РФ</a>",
    7=>"<a href=\"".$name_script."?sort=passport\" class=\"upmenu\">Наличие паспорта</a>",
    8=>"<a href=\"".$name_script."?sort=med\" class=\"upmenu\">Наличие мед.книжки</a>",       
    9=>'Расширенные требования',
    10=>''
);

//строка поиска
#$inputLine = new Input($db);
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'objRequest'=>$this->Input->inputSelect('search[objRequest]',50, "SELECT r.id, concat('Заявка №',r.id,' заказа №',r.id_order,' ',o.name) FROM ".$pref."orders_requests as r, ".$pref."clients_object as o WHERE o.id=r.id_object"),
    'gender'=>'',
    'ageAfter'=>'',
    'ageBefore'=>'',
    'national'=>'',
    'passport'=>'',
    'med'=>'',
    'objSet'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.state as state,
tbl1.id_request as objRequest, 
tbl1.gender as gender, 
tbl1.age_after as ageAfter, 
tbl1.age_before as ageBefore, 
tbl1.national as national, 
tbl1.passport as passport,  
tbl1.med as med,
tbl1.id_set as objSetRequest
FROM ".$pref.$this->table_name." as tbl1 
";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//
//дополнительные данные
if(count($result)>0){
    for($k=0;$k<count($result);$k++){
        $id=$this->result[$k]['id'];

        $this->arr=getApi3($this->conn, $pref, "setReq","setReqAll","id",$this->result[$k]['objSetRequest'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objSetRequest']=$this->arr['setReqAll']['id'][$this->result[$k]['objSetRequest']];
    }
}

?>