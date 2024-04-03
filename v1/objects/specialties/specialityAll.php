<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех специальностях           //
//                                                             //
//-------------------------------------------------------------//

$name_script="specialties.dhtml";
$name_script_info="speciality_info.dhtml";

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
    1=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    2=>'Примечание',
    3=>''
);

//строка поиска
#$inputLine = new Input($db);
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'name'=>$this->Input->inputText('like[name]',50),
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.name as name,
    tbl1.note as note
    FROM ".$pref.$this->table_name." as tbl1 
";


//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//
?>