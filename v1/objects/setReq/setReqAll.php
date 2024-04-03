<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о расширенных и клиентозависимых требованиях    format2   //
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
    1=>"<a href=\"".$name_script."?sort=name\" class=\"upmenu\">Название</a>",
    2=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Примечание</a>",       
    3=>'Изменил',
    4=>'создан автоматически',
    5=>'Требования',
    6=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'name'=>'',
    'note'=>'',
    'personChange'=>'',
    'auto'=>'',
    'arrObjRequirements'=>$this->Input->inputSelect('search[arrObjRequirements]',50,'SELECT 1, 1 '),
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.name as name, 
    tbl1.note as note,
    tbl1.id_person_change as personChange,
    tbl1.auto as auto,
    tbl1.id as arrObjRequirements
    FROM ".$pref.$this->table_name." as tbl1 ";

//---------------------общая часть для всех ALL в отдельном файле---------------//
if($this->call_from_api=='Yes')
    include $GLOBALS['ver'].'/objects/objectFileAllResultFromApi.php';
else
    include_once $GLOBALS['ver'].'/objects/objectFileAllResult2.php';
//----------------------в нем формируется Result --------------------------------//
if(count($result)>0){
    for($k=0;$k<count($result);$k++){
        $id=$this->result[$k]['id'];

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];

        $this->result[$k]['arrObjRequirements']=array();
        $res_req=DBFetchNew($this->conn, "SELECT s.id_requirement, s.hidden, s.required, g.name, g.clients FROM ".$pref."set_req as s,".$pref."global_requirements as g WHERE g.id=s.id_requirement and s.id_set=".$id);
        for($i=0;$i<count($res_req);$i++){
            list($id_requirement, $hidden, $required, $name, $clients)=$res_req[$i];
            $this->result[$k]['arrObjRequirements'][$i]=array('id'=>$id_requirement,'name'=>$name, 'hidden'=>$hidden,'required'=>$required, 'clients'=>$clients);
        }

    }
}

?>