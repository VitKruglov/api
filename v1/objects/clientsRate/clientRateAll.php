<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех ставках  format2                 //
//                                                             //
//-------------------------------------------------------------//

$name_script="clients_rate.dhtml";
$name_script_info="client_rate_info.dhtml";


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
    1=>"Изменил",
    2=>"Дата изменения",
    3=>"Активен",    
    4=>"<a href=\"".$name_script."?sort=id_speciality\" class=\"upmenu\">Специальность</a>",
    5=>"<a href=\"".$name_script."?sort=id_object\" class=\"upmenu\">Объект</a>",
    6=>"<a href=\"".$name_script."?sort=id_group\" class=\"upmenu\">Группа объектов</a>",    
    7=>"<a href=\"".$name_script."?sort=id_client\" class=\"upmenu\">Клиент</a>",    
    8=>"<a href=\"".$name_script."?sort=id_department\" class=\"upmenu\">Отдел</a>",
    9=>'Цена за час',
    10=>'Всего часов',
    11=>'Цена за смену',
    12=>'Цена за единицу',
    13=>'Тип единицы',
    14=>'Базовые требования',
    15=>'Расширенные требования',
    16=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'idPersonChange'=>'',
    'dtChange'=>'',
    'state'=>'',
    'objSpeciality'=>$this->Input->inputSelect('search[objSpeciality]',50,'SELECT id, name FROM '.$pref.'speciality'),
    'objObject'=>$this->Input->inputSelect('search[objObject]',50,'SELECT id, name FROM '.$pref.'clients_object'),    
    'objGroup'=>$this->Input->inputSelect('search[objGroup]',50,'SELECT id, name FROM '.$pref.'clients_group'),    
    'objClient'=>$this->Input->inputSelect('search[objClient]',50,'SELECT id, name FROM '.$pref.'clients'),
    'objDepartment'=>$this->Input->inputSelect('search[objDepartment]',50,"SELECT id, name FROM ".$pref.'clients_department'),
    'rateHour'=>'',
    'hours'=>'',
    'rateDay'=>'', 
    'rateUnit'=>'', 
    'objTypeUnit'=>'', 
    'objReq'=>'', 
    'objSet'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.id_person_change as idPersonChange,
    tbl1.dt_change as dtChange,
    tbl1.state as state,
    tbl1.id_speciality as objSpeciality, 
    tbl4.id as objObject, 
    tbl4.id_group as objGroup, 
    tbl6.id as objClient, 
    tbl1.id_department as objDepartment, 
    tbl1.rate_hour as rateHour, 
    tbl1.hours as hours, 
    tbl1.rate_day as rateDay, 
    tbl1.rate_unit as rateUnit, 
    tbl1.id_type_unit as objTypeUnit, 
    tbl1.id_req as objReq, 
    tbl1.id_set as objSet
    FROM ".$pref.$this->table_name." as tbl1 
    LEFT JOIN ".$pref."speciality as tbl2 ON tbl2.id=tbl1.id_speciality
    LEFT JOIN ".$pref."clients_department as tbl3 ON tbl3.id=tbl1.id_department
    LEFT JOIN ".$pref."clients_object as tbl4 ON tbl4.id=tbl3.id_object
    LEFT JOIN ".$pref."clients_group as tbl5 ON tbl5.id=tbl4.id_group
    LEFT JOIN ".$pref."clients as tbl6 ON tbl6.id=tbl5.id_client
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
        $this->result[$k]['idPersonChange']=$this->arr['person'][$this->result[$k]['idPersonChange']];

        $this->result[$k]['objTypeUnit']=$this->arr['objTypeUnit'][$this->result[$k]['objTypeUnit']];
        $this->result[$k]['objSpeciality']=$this->arr['objSpeciality'][$this->result[$k]['objSpeciality']];
        $this->result[$k]['objDepartment']=$this->arr['objDepartment'][$this->result[$k]['objDepartment']];
        $this->result[$k]['objObject']=$this->arr['objObject'][$this->result[$k]['objObject']];
        $this->result[$k]['objGroup']=$this->arr['objGroup'][$this->result[$k]['objGroup']];
        $this->result[$k]['objClient']=$this->arr['objClient'][$this->result[$k]['objClient']];
        $this->result[$k]['objReq']=$this->arr['objReq'][$this->result[$k]['objReq']];

        if($this->result[$k]['objSet']>0){
            $this->arr=getApi3($this->conn, $pref, "setReq","setReqAll","id",$this->result[$k]['objSet'], NULL, NULL, $this->domain,$this->arr);
            $this->result[$k]['objSet']=$this->arr['setReqAll']['id'][$this->result[$k]['objSet']];
        }
    }

}

?>