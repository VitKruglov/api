<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех организациях АК         //
//                                                             //
//-------------------------------------------------------------//

$name_script="companies.dhtml";
$name_script_info="company_info.dhtml";


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
    2=>"Изменил",
    3=>"Дата изменения",
    4=>"<a href=\"".$name_script."?sort=type\" class=\"upmenu\">Тип</a>",
    5=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    6=>"полное название",
    7=>"ИНН",
    8=>'КПП',
    9=>'ОГРН',
    10=>'примечание'
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT 1,'Да' UNION SELECT 0,'Нет(удален)'"),
    'personChange'=>'',
    'dtChange'=>'',
    'type'=>$this->Input->inputSelect('search[type]',50,"SELECT 'indiv','Физическое лицо' UNION SELECT 'entity','Юридическое лицо'"),
    'name'=>$this->Input->inputText('like[name]',20),
    'realname'=>$this->Input->inputText('like[realname]',20),
    'inn'=>$this->Input->inputText('search[inn]',20),
    'kpp'=>$this->Input->inputText('search[kpp]',20),
    'ogrn'=>$this->Input->inputText('search[ogrn]',20),
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.state as state, 
tbl1.id_person_change as personChange,
tbl1.dt_change as dtChange,
tbl1.type as type,
tbl1.name as name, 
tbl1.realname as realname, 
tbl1.inn as inn,
tbl1.kpp as kpp, 
tbl1.ogrn as ogrn, 
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
    }
}
?>