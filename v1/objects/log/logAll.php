<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о логах пользователя           //
//                                                             //
//-------------------------------------------------------------//

$name_script="pay_types.dhtml";
$name_script_info="pay_type_info.dhtml";



if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    if(isset($data['search']['dtBegin'])){
        $dt_from=$data['search']['dtBegin'];
        unset($data['search']['dtBegin']);
    }elseif(isset($data['like']['dtBegin'])){
        $dt_from=$data['like']['dtBegin'];
        unset($data['like']['dtBegin']);
    }elseif(isset($this->getParam['get']['dtBegin']))
        $dt_from=$this->getParam['get']['dtBegin'];

    if(isset($data['search']['dtEnd'])){
        $dt_to=$data['search']['dtEnd'];
        unset($data['search']['dtEnd']);
    }elseif(isset($data['like']['dtEnd'])){
        $dt_to=$data['like']['dtEnd'];
        unset($data['like']['dtEnd']);
    }elseif(isset($this->getParam['get']['dtEnd']))
        $dt_to=$this->getParam['get']['dtEnd'];     

    if(strlen($dt_from)>=10 and strlen($dt_to)>=10)
        $where=" and dt>='$dt_from' and dt<='$dt_to'";
    elseif(strlen($dt_from)>=10)
        $where=" and dt>='$dt_from'";
    else
        $where=" and dt>='".date("Y-m-d",time())."'";

    $session_id=$data['session_id'];
}


list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"ID",
    1=>"Пользователь",
    2=>"Имя метода API",
    3=>"id записи",
    4=>"Дата",
    5=>""
);

//строка поиска

if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'idPerson'=>$this->Input->inputSelect('search[idPerson]',50,"SELECT id,realname FROM ".$pref."persons"),
    'name'=>'',
    'idField'=>'',
    'dt'=>'' 
);

if(!$sort)
    $sort='dt DESC';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.id_person as idPerson, 
tbl1.name as name, 
tbl1.id_field as idField,
tbl1.dt as dt
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

        $this->result[$k]['idPerson']=$this->arr['person'][$this->result[$k]['idPerson']];

    }
}
?>