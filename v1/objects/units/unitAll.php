<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех ролях                //
//                                                             //
//-------------------------------------------------------------//

$name_script="units.dhtml";
$name_script_info="unit_info.dhtml";

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];
    if(isset($data['search'])){
            $search=$data['search'];
            foreach($search as $type=>$val)
                foreach($val as $name_search=>$val2)
                    if($val2!=NULL and $type=='int') $where = $where." AND ".$name_search."=".$val2;
                    elseif($val2!=NULL and $type=='text') $where = $where." AND ".$name_search." LIKE '%".$val2."%'";
    }
    $session_id=$data['session_id'];
}

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    2=>''
);

//строка поиска
#$inputLine = new Input($db);
if($this->lrv==1)
$arr_sort['search']=array(
    0=>'',
    1=>$this->Input->inputText('search[text][tbl1.name]',50),
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, tbl1.name as name
    FROM ".$pref.$this->table_name." as tbl1 
    WHERE 1=1 $where 
    ORDER BY $sort";

$result=DBFetchNew($this->conn, $query);

//---------------------общая часть для всех ALL в отдельном файле---------------//
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult.php';
//----------------------в нем формируется Result --------------------------------//


?>