<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех группах объектов  format 2  //
//                                                             //
//-------------------------------------------------------------//

$name_script="clients_group.dhtml";
$name_script_info="client_group_info.dhtml";

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
    2=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активный</a>",
    3=>"<a href=\"".$name_script."?sort=id_client\" class=\"upmenu\">Клиент</a>",
    4=>"<a href=\"".$name_script."?sort=id_person\" class=\"upmenu\">Ответственный менеджер</a>",
    5=>"Изменил",
    6=>"Дата изменения",
    7=>'Дополнительная информация',
    8=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'name'=>$this->Input->inputText('like[name]',50),
    'state'=>$this->Input->inputSelect('search[[state]',50, "SELECT '1', 'Да' UNION SELECT '0', 'Нет'"),
    'objClient'=>$this->Input->inputSelect('search[objClient]',50,'SELECT id, name FROM '.$pref.'clients'),
    'objPersonResponsibleGroup'=>$this->Input->inputSelect('search[objPersonResponsibleGroup]',50,'SELECT id, realname FROM '.$pref.'persons'),
    'personChange'=>'',
    'dtChange'=>'',
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.name as name, 
    tbl1.state as state, 
    tbl1.id_client as objClient,
    tbl1.id_person as objPersonResponsibleGroup,
    tbl1.id_person_change as personChange,
    tbl1.dt_change as dtChange,
    tbl1.note as note
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

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
        $this->result[$k]['objPersonResponsibleGroup']=$this->arr['objPerson'][$this->result[$k]['objPersonResponsibleGroup']];

        $this->arr=getApi3($this->conn, $pref, "clients","clientAll","id",$this->result[$k]['objClient'], NULL, NULL, $this->domain,$this->arr);
        $this->result[$k]['objClient']=$this->arr['clientAll']['id'][$this->result[$k]['objClient']];
    }
}
?>