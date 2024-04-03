<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о деталях выплат               //
//                                                             //
//-------------------------------------------------------------//

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
    0=>"ID",
    1=>"Активность",
    3=>"Изменил",
    4=>"Дата изменения",
    5=>"Выплата",
    6=>"Начисление",
    7=>"Сумма",
    8=>''
);

//строка поиска
#$inputLine = new Input($db);
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT 1,'Да' UNION SELECT 0,'Нет(удален)'"),
    'personChange'=>'',
    'dtChange'=>'',
    'objPayPay'=>'',
    'objPayBalance'=>'',
    'sum'=>''
);

if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.state as state, 
tbl1.id_person_change as personChange,
tbl1.dt_change as dtChange,
tbl1.id_pay_pay as objPayPay,
tbl1.id_pay_balance as objPayBalance,
tbl1.sum as sum
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

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];

        $this->arr=getApi3($this->conn, $pref, "payPay","payPayAll","id",$this->result[$k]['objPayPay'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objPayPay']=$this->arr['payPayAll']['id'][$this->result[$k]['objPayPay']];

        $this->arr=getApi3($this->conn, $pref, "payBalance","payBalanceAll","id",$this->result[$k]['objPayBalanc'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objPayBalanc']=$this->arr['payBalanceAll']['id'][$this->result[$k]['objPayBalanc']];
    }
}
?>