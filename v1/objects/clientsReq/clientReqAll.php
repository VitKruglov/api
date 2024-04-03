<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о базовых требованиях к ставкам     format2    //
//                                                             //
//-------------------------------------------------------------//

$name_script="clients_req.dhtml";
$name_script_info="client_req_info.dhtml";


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
    1=>"<a href=\"".$name_script."?sort=gender\" class=\"upmenu\">Пол</a>",
    2=>"<a href=\"".$name_script."?sort=age_after\" class=\"upmenu\">Возраст от</a>",
    3=>"<a href=\"".$name_script."?sort=age_before\" class=\"upmenu\">Возраст до</a>",
    4=>"<a href=\"".$name_script."?sort=national\" class=\"upmenu\">Гражданство РФ</a>",
    5=>"<a href=\"".$name_script."?sort=passport\" class=\"upmenu\">Наличие паспорта</a>",
    6=>"<a href=\"".$name_script."?sort=med\" class=\"upmenu\">Наличие мед.книжки</a>",       
    7=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'gender'=>$this->Input->inputSelect('search[gender]',50, "SELECT NULL, NULL UNION SELECT 'male', 'муж.' UNION SELECT 'female', 'жен.' UNION SELECT 'all', 'все'"),
    'ageAfter'=>'',
    'ageBefore'=>'',
    'national'=>$this->Input->inputSelect('search[national]',50, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),  
    'passport'=>$this->Input->inputSelect('search[passport]',50, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),  
    'med'=>$this->Input->inputSelect('search[med]',50, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),  
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.gender as gender, 
    age_after as ageAfter, 
    age_before as ageBefore, 
    tbl1.national as national, 
    tbl1.passport as passport,  
    tbl1.med as med
    FROM ".$pref.$this->table_name." as tbl1";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//



?>