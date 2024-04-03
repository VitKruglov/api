<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о расширенных и клиентозависимых требованиях   format 2    //
//                                                             //
//-------------------------------------------------------------//

$name_script="global_req.dhtml";
$name_script_info="global_req_info.dhtml";


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['id_rate']))
        $id_rate=$data['id_rate'];    
    if(isset($data['sort']))
        $sort=$data['sort'];

    $session_id=$data['session_id'];
}

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=clients\" class=\"upmenu\">Клиентозависимое</a>",
    2=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    3=>"<a href=\"".$name_script."?sort=type\" class=\"upmenu\">Тип</a>",
    4=>"<a href=\"".$name_script."?sort=hidden\" class=\"upmenu\">Скрытое</a>",
    5=>"<a href=\"".$name_script."?sort=required\" class=\"upmenu\">Строгость</a>",
    6=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Примечание</a>",       
    7=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'clients'=>$this->Input->inputSelect('search[clients]',50, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),
    'name'=>'',
    'idType'=>$this->Input->inputSelect('search[objTypeReq]',50, "SELECT 1,'Одежда' UNION SELECT 2,'Навык' UNION SELECT 3,'Документ'"), 
    'hidden'=>$this->Input->inputSelect('search[hidden]',50, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),  
    'required'=>$this->Input->inputSelect('search[required]',50, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'"),  
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.clients as clients, 
    tbl1.name as name, 
    tbl1.type as objTypeReq, 
    tbl1.hidden as hidden, 
    tbl1.required as required,  
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
        
        $this->result[$k]['objTypeReq']=$this->arr['objTypeReq'][$this->result[$k]['objTypeReq']];
    }
}
?>