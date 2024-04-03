<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех отделах     format2    //
//                                                             //
//-------------------------------------------------------------//

$name_script="clients_department.dhtml";
$name_script_info="client_department_info.dhtml";

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    $session_id=$data['session_id'];
}

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    2=>"<a href=\"".$name_script."?sort=id_object\" class=\"upmenu\">Объект</a>",
    3=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'name'=>$this->Input->inputText('like[name]',50),
    'objObject'=>$this->Input->inputSelect('search[objObject]',50,'SELECT id, name FROM '.$pref.'clients_object')
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.name as name, 
    tbl1.id_object as objObject
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

        $this->arr=getApi3($this->conn, $pref, "clientsObject","clientObjectAll","id",$this->result[$k]['objObject'], NULL, NULL, $this->domain,$this->arr);
        $this->result[$k]['objObject']=$this->arr['clientObjectAll']['id'][$this->result[$k]['objObject']];

    }
}
?>