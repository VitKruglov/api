<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех лидах                    //
//                                                             //
//-------------------------------------------------------------//

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];
    if(isset($data['search'])){

    }else{
        $min_id=DBFetchNew($this->conn, "SELECT id FROM ".$pref.$this->table_name." ORDER BY id DESC LIMIT 100");
        $min_id=$min_id[count($min_id)-1][0];
        $min_id=$min_id+0;
        $where=" and tbl1.id>=$min_id";
    }
    $session_id=$data['session_id'];
}



//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активный</a>",
    2=>"Дата изменения",
    3=>"Изменил",
    4=>"Имя",
    5=>'Адрес',
    6=>'Электронная почта',
    7=>'Телефон',
    8=>'Источник',
    9=>'Состояние',
    10=>'Примечание',
    11=>'Физ.лицо',
    12=>'Исполнитель',
    13=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT 1,'Да' UNION SELECT 0,'Нет(удален)'"),
    'dtChange'=>'',
    'personChange'=>'',
    'name'=>$this->Input->inputText('like[name]',20),
    'address'=>'',
    'mail'=>'',
    'phone'=>'',
    'objSource'=>'',
    'objCondition'=>'',
    'note'=>'',
    'idLpMan'=>'',
    'idLpWorker'=>'',
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
    tbl1.state as state, 
    tbl1.dt_change as dtChange,
    tbl1.id_person_change as personChange,
    tbl1.name as name, 
    tbl1.address as address,
    tbl1.mail as mail,
    tbl1.phone as phone,
    tbl1.id_source as objSource,
    tbl1.id_condition as objCondition,
    tbl1.note as note,
    tbl1.id as idLpMan,
    tbl1.id as idLpWorker
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

        list($this->result[$k]['idLpMan'])=DBQueryNew($this->conn, "SELECT id FROM ".$pref."lp_man WHERE id_lead=".$this->result[$k]['idLpMan']);
        list($this->result[$k]['idLpWorker'])=DBQueryNew($this->conn, "SELECT id FROM ".$pref."lp_worker WHERE id_man='".$this->result[$k]['idLpMan']."'");

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
        $this->result[$k]['objCondition']=$this->arr['objTypeCondition'][$this->result[$k]['objCondition']];
        $this->result[$k]['objSource']=$this->arr['objTypeSource'][$this->result[$k]['objSource']];
    }
}


?>