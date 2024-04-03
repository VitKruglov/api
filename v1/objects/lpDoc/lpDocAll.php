<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех физических лицах         //
//                                                             //
//-------------------------------------------------------------//

$name_script="lp_doc.dhtml";
$name_script_info="lp_doc_info.dhtml";


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['id_man']))
        $id_man=$data['id_man'];    
    if(isset($data['sort']))
        $sort=$data['sort'];
    $session_id=$data['session_id'];
}

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    2=>"<a href=\"".$name_script."?sort=type\" class=\"upmenu\">Тип</a>",
    3=>"id Физ.лица",
    4=>'Серия',
    5=>'Номер',
    6=>'Кем выдан',
    7=>'Дата выдачи',
    8=>'Подразделение',
    9=>'Место рождения',
    10=>'Место регистрации',
    11=>'Дата регистрации',
    12=>'Наличие временной регистрации',
    13=>'Дата временной регистрации',
    14=>'Дата рождения',
    15=>'Дата действия документа'
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'objTypeDocument'=>$this->Input->inputSelect('search[objTypeDocument]',50, "SELECT id,name FROM ".$pref."type_document"),
    'idman'=>'',
    'ser'=>'',
    'number'=>'',
    'kem'=>'',
    'dt'=>'',
    'podr'=>'',
    'mesto'=>'',
    'reg'=>'',
    'dtReg'=>'',
    'regVr'=>'',
    'dtRegVr'=>'',
    'dtBd'=>'',
    'dtEnd'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state, 
    tbl1.id_type as objTypeDocument,
    tbl1.id_man as idMan,
    tbl1.ser as ser,
    tbl1.number as number,
    tbl1.kem as kem,
    tbl1.dt as dt,
    tbl1.podr as podr,
    tbl1.mesto as mesto,
    tbl1.reg as reg,
    tbl1.dt_reg as dtReg,
    tbl1.reg_vr as regVr,
    tbl1.dt_reg_vr as dtRegVr,
    tbl1.dt_bd as dtBd,
    tbl1.dt_end as dtEnd
    FROM ".$pref.$this->table_name." as tbl1";

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

        $this->result[$k]['objTypeDocument']=$this->arr['objTypeDocument'][$this->result[$k]['objTypeDocument']];
    }
}
?>