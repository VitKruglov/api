<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о правах доступа              //
//                                                             //
//-------------------------------------------------------------//

$name_script="";
$name_script_info="";

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
    0=>"<a href=\"".$name_script."?sort=api_name\" class=\"upmenu\">Название метода API</a>",
    1=>"<a href=\"".$name_script."?sort=id_unit\" class=\"upmenu\">Роль</a>",
    2=>"<a href=\"".$name_script."?sort=type\" class=\"upmenu\">Тип прав</a>",
    3=>''
);

//строка поиска
#$inputLine = new Input($db);
$arr_sort['search']=array(
    0=>$this->Input->inputText('search[text][tbl1.name]',20),
    1=>$this->Input->inputSelect('search[int][id_unit]',50,'SELECT id, name FROM '.$pref.'unit_name'),
    2=>$this->Input->inputSelect('search[int][id_unit]',50,"SELECT '1','Да' UNION SELECT '2','Частично'"),
);

if(!$sort)
    $sort='tbl1.api_name';

$result=array();

$query="SELECT tbl1.api_name as api_name, tbl2.name as id_unit, tbl3.name as type
    FROM ".$pref."permis_unit as tbl1 
    LEFT JOIN ".$pref."unit_name as tbl2 ON tbl2.id=tbl1.id_unit
    LEFT JOIN (SELECT '1' as id,'Да' as name UNION SELECT '2','Частично') as tbl3 ON tbl3.id=tbl1.type
    WHERE 1=1 $where 
    ORDER BY $sort";

$result=DBFetchNew($this->conn, $query);


//---------------------общая часть для всех ALL в отдельном файле---------------//
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult.php';
//----------------------в нем формируется Result --------------------------------//


?>