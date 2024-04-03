<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех пользователях                //
//                                                             //
//-------------------------------------------------------------//

$name_script="persons.dhtml";
$name_script_info="person_info.dhtml";

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    list($id_owner)=explode("_",$pref);

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
    1=>"<a href=\"".$name_script."?sort=realname\" class=\"upmenu\">ФИО</a>",
    2=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Логин</a>",
    3=>"<a href=\"".$name_script."?sort=tel\" class=\"upmenu\">Телефон</a>",
    4=>"<a href=\"".$name_script."?sort=email\" class=\"upmenu\">Почта</a>",
    5=>"<a href=\"".$name_script."?sort=id_group\" class=\"upmenu\">Группа</a>",
    6=>"<a href=\"".$name_script."?sort=id_unit\" class=\"upmenu\">Роль</a>",
    7=>''
);

//строка поиска
#$inputLine = new Input($db);
$arr_sort['search']=array(
    0=>'',
    1=>$this->Input->inputText('search[text][tbl1.realname]',30),
    2=>$this->Input->inputText('search[text][tbl1.name]',20),
    3=>'',
    4=>'',
    5=>$this->Input->inputSelect('search[int][id_group]',50,'SELECT id, name FROM hd_groups'),
    6=>$this->Input->inputSelect('search[int][id_unit]',50,'SELECT id, name FROM '.$pref.'unit_name')
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.realname as realname, 
tbl1.name as name, 
tbl1.tel as tel,
tbl1.email as email,
tbl2.name as id_group, 
tbl3.name as id_unit
    FROM ".$pref.$this->table_name." as tbl1 
    LEFT JOIN (SELECT 1 as id, 'сотрудник' as name UNION SELECT 2, 'не сотрудник') tbl2 ON tbl2.id=tbl1.id_group
    LEFT JOIN ".$pref."unit_name as tbl3 ON tbl3.id=tbl1.id_unit
    ";

$result=DBFetchNew($this->conn, $query);

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//


?>