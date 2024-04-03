<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех клиентах                 //
//                                                             //
//-------------------------------------------------------------//

$name_script="owners.dhtml";
$name_script_info="owner_info.dhtml";

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
    1=>"<a href=\"".$name_script."?sort=realname\" class=\"upmenu\">Название</a>",
    2=>"<a href=\"".$name_script."?sort=address\" class=\"upmenu\">Адрес</a>",
    3=>"<a href=\"".$name_script."?sort=domain\" class=\"upmenu\">Домен</a>",
    4=>''
);

//строка поиска
#$inputLine = new Input($db);
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'realname'=>$this->Input->inputText('like[realname]',20),
    'address'=>$this->Input->inputText('like[addrees]',20),
    'domain'=>$this->Input->inputText('like[domain]',20),
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.realname as realname, 
    tbl1.address as address, 
    tbl1.domain as domain
    FROM ".$pref.$this->table_name." as tbl1 
";

//---------------------общая часть для всех ALL в отдельном файле---------------//
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//


?>