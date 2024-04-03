<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о комментариях физического лица         //
//                                                             //
//-------------------------------------------------------------//

$name_script="lp_note.dhtml";
$name_script_info="lp_note_info.dhtml";


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['id_man']))
        $id_man=" tbl1.id_man=".$data['id_man'];    
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
    4=>"Дата создания",
    5=>"<a href=\"".$name_script."?sort=importance\" class=\"upmenu\">Важность</a>",    
    6=>"<a href=\"".$name_script."?sort=id_man\" class=\"upmenu\">Физ.лицо</a>",
    7=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Описание</a>",
    8=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'personChange'=>'',
    'dtChange'=>'',
    'dt'=>'',
    'importance'=>$this->Input->inputSelect('search[importance]',50, "SELECT 1, 'Обычный' UNION SELECT 2, 'ВАЖНО'"),
    'idMan'=>$this->Input->inputSelect('search[idMan]',50, "SELECT m.id, concat(m.sname,' ',m.name) as name FROM ".$pref."lp_man as m ORDER BY m.name"),
    'note'=>$this->Input->inputText('like[note]',20)
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state,
    tbl1.id_person_change as personChange,
    tbl1.dt_change as dtChange,
    tbl1.dt as dt,
    tbl1.importance as importance, 
    tbl1.id_man as idMan, 
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