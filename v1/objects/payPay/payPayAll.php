<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех выплатах                //
//                                                             //
//-------------------------------------------------------------//

$name_script="pay_pay.dhtml";
$name_script_info="pay_pay_info.dhtml";



if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    if(isset($data['search']['from'])){
        $dt_from=$data['search']['from'];
        unset($data['search']['from']);
    }elseif(isset($data['like']['from'])){
        $dt_from=$data['like']['from'];
        unset($data['like']['from']);
    }elseif(isset($this->getParam['get']['from']))
        $dt_from=$this->getParam['get']['from'];

    if(isset($data['search']['to'])){
        $dt_to=$data['search']['to'];
        unset($data['search']['to']);
    }elseif(isset($data['like']['to'])){
        $dt_to=$data['like']['to'];
        unset($data['like']['to']);
    }elseif(isset($this->getParam['get']['to']))
        $dt_to=$this->getParam['get']['to'];      

    if(strlen($dt_from)==10 and strlen($dt_to)==10)
        $where.=" and (DATE_FORMAT(tbl1.dt_pay,'%Y-%m-%d')>='$dt_from' and DATE_FORMAT(tbl1.dt_pay,'%Y-%m-%d')<='$dt_to')";
    elseif(strlen($dt_from)==10)
        $where.=" and DATE_FORMAT(tbl1.dt_pay,'%Y-%m-%d')>='$dt_from'";
    else
        $where.=" and (DATE_FORMAT(tbl1.dt_pay,'%Y-%m-%d')>=DATE_FORMAT(NOW(),'%Y-%m-01') and DATE_FORMAT(tbl1.dt_pay,'%Y-%m-%d')<=DATE_FORMAT(NOW(),'%Y-%m-31'))";

    if(isset($data['search']['id'])) $where=NULL;

    $session_id=$data['session_id'];
}


list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    3=>"Выплатил",
    4=>"Дата выплаты",
    5=>"Утвердил",
    6=>"Дата утверждения",
    7=>"<a href=\"".$name_script."?sort=id_account\" class=\"upmenu\">Счет АК</a>",      
    8=>"<a href=\"".$name_script."?sort=id_lp_account\" class=\"upmenu\">Счет исполнителя</a>",    
    10=>"<a href=\"".$name_script."?sort=sum\" class=\"upmenu\">Сумма</a>",
    11=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Примечание</a>",
    12=>''
);

//строка поиска
#$inputLine = new Input($db);
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT 1,'Да' UNION SELECT 0,'Нет(удален)'"),
    'personPay'=>'',
    'dtPay'=>$this->Input->inputText('search[dtPay]',10),
    'personSignature'=>'',
    'dtSignature'=>'',
    'objAccount'=>$this->Input->inputSelect('search[objAccount]',50,"SELECT id,name FROM ".$pref."accounts"),    
    'objLpAccount'=>$this->Input->inputSelect('search[objLpAccount]',50,"SELECT a.id as id ,concat(w.number,' ',m.sname,' ',m.name,' ',a.card,' ',a.bank_name) as name FROM ".$pref."lp_account as a, ".$pref."lp_worker as w, ".$pref."lp_man as m WHERE w.id=a.id_worker and m.id=w.id_man ORDER BY name"),   
    'sum'=>'',
    'note'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.state as state, 
tbl1.id_person_pay as personPay,
tbl1.dt_pay as dtPay,
tbl1.id_person_signature as personSignature,
tbl1.dt_signature as dtSignature,
tbl1.id_account as objAccount,
tbl1.id_lp_account as objLpAccount,
tbl1.sum as sum,
tbl1.note as note
FROM ".$pref.$this->table_name." as tbl1";
    
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

        $this->result[$k]['personSignature']=$this->arr['person'][$this->result[$k]['personSignature']];
        $this->result[$k]['personPay']=$this->arr['person'][$this->result[$k]['personPay']];

        $this->arr=getApi3($this->conn, $pref, "accounts","accountAll","id",$this->result[$k]['objAccount'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objAccount']=$this->arr['accountAll']['id'][$this->result[$k]['objAccount']];

        $this->arr=getApi3($this->conn, $pref, "lpAccount","lpAccountAll","id",$this->result[$k]['objLpAccount'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objLpAccount']=$this->arr['lpAccountAll']['id'][$this->result[$k]['objLpAccount']];
    }
}
?>