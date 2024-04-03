<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех клиентах                 //
//                                                             //
//-------------------------------------------------------------//

$name_script="modules.dhtml";
$name_script_info="module_info.dhtml";

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
    2=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Примечание</a>",
    3=>''
);

//строка поиска
#$inputLine = new Input($db);
$arr_sort['search']=array(
    0=>'',
    1=>$this->Input->inputText('search[text][tbl1.name]',20),
    2=>$this->Input->inputText('search[text][tbl1.note]',20)
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, tbl1.name as name, tbl1.note as note
    FROM ".$pref.$this->table_name." as tbl1 
    WHERE 1=1 $where 
    ORDER BY $sort";

$result=DBFetchNew($this->conn, $query);

#$result2=DBExecuteNew($this->conn, "update modules_tables SET copy_data='y' where name_table='global_requirements' and module_id=1");

//---------------------общая часть для всех ALL в отдельном файле---------------//
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult.php';
//----------------------в нем формируется Result --------------------------------//


?>