<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех заказах      format2     //
//                                                             //
//-------------------------------------------------------------//

$name_script="orders.dhtml";
$name_script_info="order_info.dhtml";

if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    if(isset($data['search']['dtBegin'])){
        $dt_from=$data['search']['dtBegin'];
        unset($data['search']['dtBegin']);
    }elseif(isset($data['like']['dtBegin'])){
            $dt_from=$data['like']['dtBegin'];
            unset($data['like']['dtBegin']);
    }elseif(isset($this->getParam['get']['from']))
        $dt_from=$this->getParam['get']['from'];

    if(isset($data['search']['dtEnd'])){
        $dt_to=$data['search']['dtEnd'];
        unset($data['search']['dtEnd']);
    }elseif(isset($data['like']['dtEnd'])){
        $dt_to=$data['like']['dtEnd'];
        unset($data['like']['dtEnd']);
    }elseif(isset($this->getParam['get']['to']))
        $dt_to=$this->getParam['get']['to'];     

    if(strlen($dt_from)==10 and strlen($dt_to)==10)
        $query="SELECT id_order FROM ".$pref."orders_requests WHERE DATE_FORMAT(dt_begin,'%Y-%m-%d')>='$dt_from' and DATE_FORMAT(dt_begin,'%Y-%m-%d')<='$dt_to' GROUP BY id_order";
    elseif(strlen($dt_from)==10)
        $query="SELECT id_order FROM ".$pref."orders_requests WHERE DATE_FORMAT(dt_begin,'%Y-%m-%d')>='$dt_from' GROUP BY id_order";
    else
        $query=" SELECT id_order FROM ".$pref."orders_requests WHERE DATE_FORMAT(dt_begin,'%Y-%m-%d')>=DATE_FORMAT(NOW(),'%Y-%m-01') GROUP BY id_order";

    if(isset($data['search']['id']))
        $where=NULL;
    else{
        $result=DBFetchNew($this->conn, $query);
        if(count($result)>0){
            $where.=" and tbl1.id IN (";
            for($i=0;$i<count($result);$i++){
                list($id_order)=$result[$i];
                $where.="$id_order,";
            }
            $where=substr($where,0,strlen($where)-1).")";
        }
    }

    $session_id=$data['session_id'];
}

list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    2=>"<a href=\"".$name_script."?sort=id_client\" class=\"upmenu\">Клиент</a>",
    3=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Дополнительная информация</a>",
    4=>"Кол-во заявок",
    5=>"Минимальная дата",
    6=>"Максимальня дата",
    4=>''
);

//строка поиска
#$inputLine = new Input($db);
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id, id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'objClient'=>$this->Input->inputSelect('search[objClient]',50,'SELECT id, name FROM '.$pref.'clients'),
    'note'=>$this->Input->inputText('like[note]',50), 
    'cntRequests'=>'',
    'dtMin'=>'',
    'dtMax'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.state as state, 
tbl1.id_client as objClient,
tbl1.note as note,
NULL as cntRequests,
NULL as dtMin,
NULL as dtMax 
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

        $this->result[$k]['cntRequests']=$this->arr['orderRequests'][$id]['cntRequests'];
        $this->result[$k]['dtMin']=$this->arr['orderRequests'][$id]['dtMin'];
        $this->result[$k]['dtMax']=$this->arr['orderRequests'][$id]['dtMax'];

        $this->arr=getApi3($this->conn, $pref, "clients","clientAll","id",$this->result[$k]['objClient'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objClient']=$this->arr['clientAll']['id'][$this->result[$k]['objClient']];
    }
}

?>