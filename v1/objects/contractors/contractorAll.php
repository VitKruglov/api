<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех контрагентах     format2     //
//                                                             //
//-------------------------------------------------------------//


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
    4=>"<a href=\"".$name_script."?sort=type\" class=\"upmenu\">Тип (физ.лицо)</a>",
    5=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    6=>"<a href=\"".$name_script."?sort=egrul\" class=\"upmenu\">Название по ЕГРЮЛ</a>",
    7=>"<a href=\"".$name_script."?sort=inn\" class=\"upmenu\">ИНН</a>",
    8=>"<a href=\"".$name_script."?sort=kpp\" class=\"upmenu\">КПП</a>",
    9=>"<a href=\"".$name_script."?sort=ogrn\" class=\"upmenu\">ОГРН</a>",
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
    'type'=>$this->Input->inputSelect('search[type]',50,"SELECT '1','Да' UNION SELECT '0','Нет'"),
    'name'=>$this->Input->inputText('like[name]',50),
    'egrul'=>$this->Input->inputText('like[egrul]',50),
    'inn'=>$this->Input->inputText('like[inn]',50),
    'kpp'=>$this->Input->inputText('like[kpp]',50),
    'ogrn'=>$this->Input->inputText('like[ogrn]',50),
    'note'=>$this->Input->inputText('like[note]',50),    
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state,
    tbl1.dt_change as dtChange,
    tbl1.id_person_change as personChange,
    tbl1.type as type,
    tbl1.name as name, 
    tbl1.egrul as egrul, 
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
if(count($result)>0){
    for($k=0;$k<count($result);$k++){
        $id=$this->result[$k]['id'];

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
        
    }

}

?>