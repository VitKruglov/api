<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех типах платежей           //
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
    $session_id=$data['session_id'];
}


list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    2=>"Изменил",
    3=>"Дата изменения",
    4=>"<a href=\"".$name_script."?sort=type\" class=\"upmenu\">Тип</a>",
    5=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    5=>"Название типа операции",
    6=>'Примечание',
    7=>'Основная операция',
    8=>''
);

//строка поиска
#$inputLine = new Input($db);
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT 1,'Да' UNION SELECT 0,'Нет(удален)'"),
    'personChange'=>'',
    'dtChange'=>'',
    'type'=>$this->Input->inputSelect('search[type]',50,"SELECT 1,'Увеличение' UNION SELECT -1,'Уменьшение'"),
    'name'=>$this->Input->inputText('like[name]',20),
    'nameOperation'=>$this->Input->inputText('search[nameOperation]',20),
    'note'=>'',
    'primary'=>''
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
tbl1.name_operation as nameOperation, 
tbl1.note as note,
tbl1.primary as 'primary'
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