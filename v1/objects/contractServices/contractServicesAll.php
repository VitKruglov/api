<?php
//---------------------------------------------------------------------------//
//                                                                            //
//        получение информации о всех услугах договоров контрагентах     format2     //
//                                                                            //
//---------------------------------------------------------------------------//


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    $session_id=$data['session_id'];
}

list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    2=>"Дата изменения",
    3=>"Изменил",
    4=>"Тип услуги",
    5=>"<a href=\"".$name_script."?sort=id_group\" class=\"upmenu\">Группа</a>",
    6=>"<a href=\"".$name_script."?sort=id_object\" class=\"upmenu\">Объект</a>",
    7=>"<a href=\"".$name_script."?sort=id_department\" class=\"upmenu\">Отдел</a>",
    8=>'Единиц в одном часе',
    9=>'Часов в одной единице',
    10=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Дополнительная информация</a>",
    11=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'dtChange'=>'',
    'personChange'=>'',
    'objTypeServices'=>'',
    'objGroup'=>$this->Input->inputSelect('search[objGroup]',50,"SELECT 0,'Все' UNION SELECT id,name FROM ".$pref."clients_group"),
    'objObject'=>$this->Input->inputSelect('search[objObject]',50,"SELECT 0,'Все' UNION SELECT id,name FROM ".$pref."clients_object"),
    'objDepartment'=>$this->Input->inputSelect('search[objDepartment]',50,"SELECT 0,'Все' UNION SELECT id,name FROM ".$pref."clients_department"),
    'unitsHour'=>'',  
    'hourUnits'=>'',  
    'note'=>$this->Input->inputText('like[note]',50),    
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state,
    tbl1.dt_change as dtChange,
    tbl1.id_person_change as personChange,
    tbl1.id_type_services as objTypeServices, 
    tbl1.id_group as objGroup,
    tbl1.id_object as objObject,
    tbl1.id_department as objDepartment,
    tbl1.units_hour as unitsHour, 
    tbl1.hour_units as hourUnits, 
    tbl1.note as note
    FROM ".$pref.$this->table_name." as tbl1 
";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//
$this->arr['objGroup'][0]='Все';
$this->arr['objObject'][0]='Все';
$this->arr['objDepartment'][0]='Все';
if(count($result)>0){
    for($k=0;$k<count($result);$k++){
        $id=$this->result[$k]['id'];

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
        $this->result[$k]['objGroup']=$this->arr['objGroup'][$this->result[$k]['objGroup']];
        $this->result[$k]['objObject']=$this->arr['objObject'][$this->result[$k]['objObject']];
        $this->result[$k]['objDepartment']=$this->arr['objDepartment'][$this->result[$k]['obDepartment']];
        $this->result[$k]['objTypeServices']=$this->arr['objTypeServices'][$this->result[$k]['objTypeServices']];    
    }

}

?>