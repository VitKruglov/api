<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех специальностях           //
//                                                             //
//-------------------------------------------------------------//

$name_script="lpspecialties.dhtml";
$name_script_info="lpspeciality_info.dhtml";

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
    0=>"<a href=\"".$name_script."?sort=id_worker\" class=\"upmenu\">исполнитель</a>",
    1=>"<a href=\"".$name_script."?sort=id_cpeciality\" class=\"upmenu\">специализация</a>",
    2=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Примечание</a>",
    3=>''
);

//строка поиска
#$inputLine = new Input($db);
if($this->lrv==1)
$arr_sort['search']=array(
    'idWorker'=>$this->Input->inputSelect('search[idWorker]',50, "SELECT id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,''),if(bd IS NOT NULL and bd!='',concat(' (',bd,')'),'')) FROM ".$pref."lp_man"),
    'idSpeciality'=>$this->Input->inputSelect('search[idSpeciality]',50, "SELECT id, name FROM ".$pref."specialities"),
    'note'=>'',
);

if(!$sort)
    $sort='tbl1.id_worker';

$result=array();

$query="SELECT tbl1.id_worker as idWorker, 
    tbl1.id_speciality as idSpeciality, 
    tbl2.name as name,
    tbl1.note as note
    FROM ".$pref.$this->table_name." as tbl1 
    LEFT JOIN ".$pref."speciality as tbl2 ON tbl2.id=tbl1.id_speciality
    ";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//

?>