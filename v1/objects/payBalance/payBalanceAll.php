<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех платежах    format2      //
//                                                             //
//-------------------------------------------------------------//

$name_script="pay_balance.dhtml";
$name_script_info="pay_balance_info.dhtml";


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];
    
    if(isset($data['search']['dtFrom'])){
        $dt_from=$data['search']['dtFrom'];
        unset($data['search']['dtFrom']);
    }elseif(isset($data['like']['dtFrom'])){
        $dt_from=$data['like']['dtFrom'];
        unset($data['like']['dtFrom']);
    }elseif(isset($this->getParam['get']['from']))
        $dt_from=$this->getParam['get']['from'];

    if(isset($data['search']['dtTo'])){
        $dt_to=$data['search']['dtTo'];
        unset($data['search']['dtTo']);
    }elseif(isset($data['like']['dtTo'])){
        $dt_to=$data['like']['dtTo'];
        unset($data['like']['dtTo']);
    }elseif(isset($this->getParam['get']['to']))
        $dt_to=$this->getParam['get']['to'];   
    
    if(isset($data['search']['dt'])){
        $dt=$data['search']['dt'];
        unset($data['search']['dt']);
    }elseif(isset($data['like']['dt'])){
        $dt=$data['like']['dt'];
        unset($data['like']['dt']);
    }elseif(isset($this->getParam['get']['dt']))
        $dt=$this->getParam['get']['dt'];   

    if(strlen($dt)==10 )
        $where.=" and (DATE_FORMAT(tbl1.dt,'%Y-%m-%d')>='$dt')";
    elseif(strlen($dt_from)==10 and strlen($dt_to)==10)
        $where.=" and (DATE_FORMAT(tbl1.dt,'%Y-%m-%d')>='$dt_from' and DATE_FORMAT(tbl1.dt,'%Y-%m-%d')<='$dt_to')";
    elseif(strlen($dt_from)==10)
        $where.=" and (DATE_FORMAT(tbl1.dt,'%Y-%m-%d')>='$dt_from')";
    elseif(!isset($data['search']['idOperation']) and !isset($data['search']['objContract']))
        $where.=" and (DATE_FORMAT(tbl1.dt,'%Y-%m-%d')>=DATE_FORMAT(NOW(),'%Y-%m-01') and DATE_FORMAT(tbl1.dt,'%Y-%m-%d')<=DATE_FORMAT(NOW(),'%Y-%m-31'))";

    if(isset($data['search']['id'])) $where=NULL;

    $session_id=$data['session_id'];
}


list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"Активность",    
    2=>"Дата начисления",
    3=>"Изменил",
    4=>"Дата изменения",
    5=>"Заверил",
    6=>"Дата заверения",    
    7=>"<a href=\"".$name_script."?sort=id_type\" class=\"upmenu\">Тип платежа</a>",
    8=>"Сущность",
    9=>"<a href=\"".$name_script."?sort=id_contract\" class=\"upmenu\">Договор</a>",
    10=>"Ставка",
    11=>"<a href=\"".$name_script."?sort=sum\" class=\"upmenu\">Сумма</a>",    
    12=>"<a href=\"".$name_script."?sort=sum_paid\" class=\"upmenu\">Выплачено</a>",    
    13=>"Цена за единицу",
    14=>"Цена за день",
    15=>"Цена за час",
    16=>"Кол-во часов",
    17=>"Кол-во единиц",
    18=>"Тип единицы",
    19=>"Дополнительная информация",
    20=>'Название операции',
    21=>'Сумма за единицы',
    22=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT '1','Да' UNION SELECT '0','Нет(удален)'"),
    'dt'=>$this->Input->inputText('like[dt]',5),
    'personChange'=>'',
    'dtChange'=>'',
    'personSignature'=>'',
    'dtSignature'=>'',
    'objType'=>$this->Input->inputSelect('search[objType]',50,"SELECT id, name FROM ".$pref."pay_type"),
    'idOperation'=>'',
    'objContract'=>$this->Input->inputSelect('search[objContract]',50,"SELECT w.id as id, concat(t.name,' ',m.sname,' ',m.name) FROM ".$pref."lp_contract as c, ".$pref."lp_worker as w, ".$pref."lp_man as m, type_contract as t WHERE t.id=c.type and w.id=c.id_worker and m.id=w.id_man"),
    'objRate'=>$this->Input->inputSelect('search[objRate]',50,"SELECT id, id FROM ".$pref."clients_rate"),
    'sum'=>'',
    'sumPaid'=>'',
    'rateUnit'=>'',
    'rateDay'=>'',
    'rateHour'=>'',
    'houre'=>'',
    'cnt'=>'',
    'objTypeUnitBalance'=>'',
    'note'=>'',
    'nameOperation'=>$this->Input->inputText('like[nameOperation]',5),
    'sumUnit'
);

if(!$sort)
    $sort='tbl1.id';

$result=array();


$query="SELECT tbl1.id as id,
tbl1.state as state,
tbl1.dt as dt, 
tbl1.id_person_change as personChange,
tbl1.dt_change as dtChange,
tbl1.id_person_signature as personSignature,
tbl1.dt_signature as dtSignature,
tbl1.id_type as objType,
tbl1.id_operation as idOperation,
tbl1.id_contract as objContract,
tbl1.id_rate as objRate,
tbl1.sum as sum,
tbl1.sum_paid as sumPaid,
tbl1.rate_unit as rateUnit,
tbl1.rate_day as rateDay,
tbl1.rate_hour as rateHour,
tbl1.hour as hour,
tbl1.cnt as cnt,
tbl1.id_type_unit as objTypeUnitBalance,
tbl1.note as note,
tbl1.name_operation as nameOperation,
tbl1.sum_unit as sumUnit
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

        $this->result[$k]['personChange']=$this->arr['person'][$this->result[$k]['personChange']];
        $this->result[$k]['personSignature']=$this->arr['person'][$this->result[$k]['personSignature']];

        $this->arr=getApi3($this->conn, $pref, "payTypes","payTypeAll","id",$this->result[$k]['objType'], NULL, NULL, NULL ,$this->arr);
        $this->result[$k]['objType']=$this->arr['payTypeAll']['id'][$this->result[$k]['objType']];

        $this->result[$k]['objContract']=$this->arr['objContract'][$this->result[$k]['objContract']];

        $this->result[$k]['objTypeUnitBalance']=$this->arr['objTypeUnit'][$this->result[$k]['objTypeUnitBalance']];

        /*
            (SELECT s.id as id, concat('Смена №',s.id,' ',if(s.dt_begin!='' and s.dt_begin IS NOT NULL, s.dt_begin,''),' - ',if(s.dt_end!='' and s.dt_end IS NOT NULL, s.dt_end,'')) as name, 'orders_shifts' as name_table, s.id_contract as id_contract FROM ".$pref."orders_shifts as s 
    UNION
    SELECT p.id, concat('ВЫПЛАТА ',p.note), 'pay_pay', NULL FROM ".$pref."pay_pay as p
    ) as tbl8 
        ON (if(tbl8.name_table='orders_shifts' and tbl8.id=tbl1.id_operation, tbl8.id_contract=tbl1.id_contract,'') or (tbl8.name_table='pay_pay' and tbl8.id=tbl1.id_operation)) and tbl8.name_table=tbl1.name_operation 
    LEFT JOIN (SELECT d.id_pay_balance as id_pay_balance, sum(d.sum) as sum FROM ".$pref."pay_detail as d GROUP BY d.id_pay_balance) as tbl9 ON tbl9.id_pay_balance=tbl1.id
    */
    }
}
?>