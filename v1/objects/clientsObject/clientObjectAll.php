<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех объектах  format2       //
//                                                             //
//-------------------------------------------------------------//

$name_script="clients_object.dhtml";
$name_script_info="client_object_info.dhtml";

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
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    3=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    4=>"<a href=\"".$name_script."?sort=address\" class=\"upmenu\">Адрес</a>",
    5=>"<a href=\"".$name_script."?sort=id_group\" class=\"upmenu\">Группа</a>",
    6=>"Клиент",
    7=>'Примечание',
    8=>'Внимание',
    9=>'Граница закрытого периода',
    10=>"Изменил границу закрытого периода",
    11=>"Дата изменения границы закрытого периода",
    12=>"Закрыл объект",
    13=>"Дата закрытия объекта",
    14=>"Изменил",
    15=>"Дата изменения",
    16=>'Адрес ФИАС',
    17=>'Широта',
    18=>'Долгота',
    19=>'Часовой пояс',
    20=>"<a href=\"".$name_script."?sort=id_person\" class=\"upmenu\">Ответственный менеджер</a>",
    21=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'name'=>$this->Input->inputText('like[name]',30),
    'address'=>$this->Input->inputText('like[address]',30),
    'objGroup'=>$this->Input->inputSelect('search[objGroup]',50,'SELECT id, name FROM '.$pref.'clients_group'),
    'objClient'=>$this->Input->inputSelect('search[objClient]',50,'SELECT id, name FROM '.$pref.'clients'),
    'note'=>'',
    'note2'=>'',
    'closePeriod'=>'',
    'personClosePeriod'=>'',
    'dtClosePeriod'=>'',
    'personClose'=>'',
    'dtClose'=>'',
    'personChange'=>'',
    'dtChange'=>'',
    'objFiassObject'=>'',
    'geoLat'=>'',
    'geoLon'=>'',
    'timeZone'=>'',
    'objPersonResponsibleObject'=>$this->Input->inputSelect('search[objPersonResponsibleObject]',50,'SELECT id, realname FROM '.$pref.'persons'),
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state,
    tbl1.name as name, 
    tbl1.address as address, 
    tbl1.id_group as objGroup, 
    tbl2.id_client as objClient,
    tbl1.note as note,
    tbl1.note2 as attention,
    tbl1.close_period as closePeriod,
    tbl1.id_person_close_period as personClosePeriod,
    tbl1.dt_close_period as dtClosePeriod,
    tbl1.id_person_close as personClose,
    tbl1.dt_close as dtClose,
    tbl1.id_person_change as personChange,
    tbl1.dt_change as dtChange,
    tbl1.id_fiass as objFiassObject,
    tbl1.geolat as geoLat,
    tbl1.geolon as geoLon,
    tbl1.timezone as timeZone,
    tbl1.id_person as objPersonResponsibleObject
    FROM ".$pref.$this->table_name." as tbl1 
    LEFT JOIN ".$pref."clients_group as tbl2 ON tbl2.id=tbl1.id_group";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//

//дополнительные данные
if(count($result)>0){
    for($k=0;$k<count($result);$k++){
        $id=$result[$k]['id'];

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
        $this->result[$k]['personClose']=$this->arr['person'][$this->result[$k]['personClose']];
        $this->result[$k]['personClosePeriod']=$this->arr['person'][$this->result[$k]['personClosePeriod']];
        $this->result[$k]['objGroup']=$this->arr['objGroup'][$this->result[$k]['objGroup']];

        $this->arr=getApi3($this->conn, $pref, "clients","clientAll","id",$this->result[$k]['objClient'], NULL, NULL, $this->domain,$this->arr);
        $this->result[$k]['objClient']=$this->arr['clientAll']['id'][$this->result[$k]['objClient']];
        $this->result[$k]['objFiassObject']=$this->arr['objFiass'][$this->result[$k]['objFiassObject']];
        $this->result[$k]['objPersonResponsibleObject']=$this->arr['objPerson'][$this->result[$k]['objPersonResponsibleObject']];
    }
}
?>