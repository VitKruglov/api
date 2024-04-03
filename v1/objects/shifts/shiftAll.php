<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о всех сменах                   //
//                                                             //
//-------------------------------------------------------------//

$name_script="shifts.dhtml";
$name_script_info="shift_info.dhtml";

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
        $where.=" and (DATE_FORMAT(tbl1.dt_begin,'%Y-%m-%d')>='$dt_from' and DATE_FORMAT(tbl1.dt_begin,'%Y-%m-%d')<='$dt_to')";
    elseif(strlen($dt_from)==10)
        $where.=" and DATE_FORMAT(tbl1.dt_begin,'%Y-%m-%d')>='$dt_from'";
    else
        $where.=" and DATE_FORMAT(tbl2.dt_begin,'%Y-%m-%d')>=DATE_FORMAT(NOW(),'%Y-%m-01')";

    if(isset($this->getParam['get']['id'])) $data['search']['id']=$this->getParam['get']['id'];
    if(isset($data['search']['id']) or isset($data['search']['objRequest']) or isset($data['search']['objContract'])) $where=NULL;

    $session_id=$data['session_id'];
}


list($id_owner)=explode("_",$pref);

//строка сортировки
$arr_sort['sort']=array(
    0=>"<a href=\"".$name_script."?sort=id\" class=\"upmenu\">ID</a>",
    1=>"<a href=\"".$name_script."?sort=state\" class=\"upmenu\">Активность</a>",
    3=>"Изменил",
    4=>"Дата изменения",
    5=>"Запланировал",
    6=>"Дата планирования",
    7=>"Отменил",
    8=>"Дата отмены",
    9=>"<a href=\"".$name_script."?sort=id_request\" class=\"upmenu\">Заявка</a>",
    10=>"<a href=\"".$name_script."?sort=id_contract\" class=\"upmenu\">Исполнитель</a>",
    11=>'Начисления',
    12=>"<a href=\"".$name_script."?sort=dt_begin\" class=\"upmenu\">Начало</a>",
    13=>"<a href=\"".$name_script."?sort=dt_end\" class=\"upmenu\">Конец</a>",
    14=>"<a href=\"".$name_script."?sort=cnt_hour\" class=\"upmenu\">Фактическое кол-во часов</a>",
    15=>"<a href=\"".$name_script."?sort=cost_hour\" class=\"upmenu\">Оплачиваемое кол-во часов</a>",
    16=>"<a href=\"".$name_script."?sort=cnt\" class=\"upmenu\">Фактическое кол-во единиц</a>",
    18=>"Планируемая дата начала",
    19=>"Планируемая дата конца",
    20=>"Планируемое кол-во часов",
    21=>"Планируемое Оплачиваемое кол-во часов",
    22=>"Планируемое кол-во единиц",
    23=>"планируемая ставка оплаты в час",
    24=>"планируемая оплата за смену",
    25=>"Плаемруемый тип единицы измерения",
    26=>"планируемая ставка оплаты для единицы измерений",
    27=>"Планируемая оплата смены по ставке",
    28=>"<a href=\"".$name_script."?sort=id_type_unit\" class=\"upmenu\">Единица измерения</a>",
    29=>"<a href=\"".$name_script."?sort=note\" class=\"upmenu\">Примечание</a>",
    30=>'Статус смены',
    31=>'Описание статуса',
    32=>'планируемая специализация',
    33=>'Сумма за смену',
    34=>'Сумма за сделку',
    35=>'Сумма за доп.операции',
    36=>'Общая сумма начислений',
    37=>''
);

//строка поиска
if($this->lrv==1)
$arr_sort['search']=array(
    'id'=>$this->Input->inputSelect('search[id]',50,"SELECT id,id FROM ".$pref.$this->table_name),
    'state'=>$this->Input->inputSelect('search[state]',50,"SELECT 1,'Да' UNION SELECT 0,'Нет(удален)'"),
    'personChange'=>'',
    'dtChange'=>'',
    'personPlan'=>'',
    'dtPlan'=>'',
    'personRemove'=>'',
    'dtRemove'=>'',
    'objRequest'=>$this->Input->inputSelect('search[objRequest]',50,'SELECT id, note FROM '.$pref.'orders_requests'),
    'objContract'=>$this->Input->inputSelect('search[objContract]',50,"SELECT w.id as id, concat(t.name,' ',m.sname,' ',m.name) FROM ".$pref."lp_contract as c, ".$pref."lp_worker as w, ".$pref."lp_man as m, type_contract as t WHERE t.id=c.type and w.id=c.id_worker and m.id=w.id_man"),
    'arrObjBalance'=>'',
    'dtBegin'=>$this->Input->inputText('like[dtBegin]',5),    
    'dtEnd'=>$this->Input->inputText('like[dtEnd]',5),    
    'cntHour'=>'',
    'costHour'=>'',
    'cnt'=>'',
    'planDtBegin'=>$this->Input->inputText('like[planDtBegin]',5),    
    'planDtEnd'=>$this->Input->inputText('like[planDtEnd]',5),    
    'planCntHour'=>'',
    'planCostHour'=>'',
    'planCnt'=>'',
    'planRateHour'=>'',
    'planRateDay'=>'',
    'objPlanTypeUnit'=>'',
    'planRateUnit'=>'',
    'planSum'=>'',
    'objTypeUnitShift'=>$this->Input->inputSelect('search[objTypeUnitShift]',50,"SELECT id,name_full FROM ".$pref."clients_type_units"),
    'note'=>'',
    'shiftStatus'=>'',
    'shiftStatusDescription'=>'',
    'objPlanSpeciality'=>$this->Input->inputSelect('search[objPlanSpeciality]',50,"SELECT id,name FROM ".$pref."lp_speciality"),
    'sumBalanceShift'=>'',
    'sumBalanceUnit'=>'',
    'sumBalanceExtra'=>'',
    'sumBalanceTotal'=>''
);


if(!$sort)
    $sort='tbl1.id';

$result=array();

$query="SELECT tbl1.id as id, 
tbl1.state as state, 
tbl1.id_person_change as personChange,
tbl1.dt_change as dtChange,
tbl1.id_person_plan as personPlan,
tbl1.dt_plan as dtPlan,
tbl1.id_person_remove as personRemove,
tbl1.dt_remove as dtRemove,
tbl1.id_request as objRequest, 
tbl1.id_contract as objContract,
tbl1.id as arrObjBalance, 
tbl1.dt_begin as dtBegin,
tbl1.dt_end as dtEnd,
tbl1.cnt_hour as cntHour,
tbl1.cost_hour as costHour,
tbl1.cnt as cnt,
tbl1.plan_dt_begin as planDtBegin,
tbl1.plan_dt_end as planDtEnd,
tbl1.plan_cnt_hour as planCntHour,
tbl1.plan_cost_hour as planCostHour,
tbl1.plan_cnt as planCnt,
tbl1.plan_rate_hour as planRateHour,
tbl1.plan_rate_day as planRateDay,
tbl1.plan_type_unit as objPlanTypeUnit,
tbl1.plan_rate_unit as planRateUnit,
tbl1.id as planSum, 
tbl1.id_type_unit as objTypeUnitShift,
tbl1.note as note,
NULL as shiftStatus,
NULL as shiftStatusDescription,
tbl1.id_speciality as objPlanSpeciality,
tbl1.sum as sumBalanceShift,
tbl1.sum_unit as sumBalanceUnit,
NULL as sumBalanceExtra,
NULL as sumBalanceTotal
FROM ".$pref.$this->table_name." as tbl1 
LEFT JOIN ".$pref."orders_requests as tbl2 ON tbl2.id=tbl1.id_request
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
        $this->result[$k]['personPlan']=$this->arr['person'][$this->result[$k]['personPlan']];
        $this->result[$k]['personRemove']=$this->arr['person'][$this->result[$k]['personRemove']];

        $id_contract=$this->result[$k]['objContract'];

        $this->result[$k]['arrObjBalance']=getApi2($this->conn, $pref, "payBalance","payBalanceAll",array("idOperation"=>$id,'objContract'=>$this->result[$k]['objContract'],'nameOperation'=>'orders_shifts','state'=>1),NULL,NULL,'array',NULL,$this->arr);

        $this->arr=getApi3($this->conn, $pref, "lpContracts","lpContractAll","id",$this->result[$k]['objContract'], NULL, NULL, $this->domain,$this->arr,$this->token, $this->session_id);
        $this->result[$k]['objContract']=$this->arr['lpContractAll']['id'][$this->result[$k]['objContract']];

        $this->result[$k]['objTypeUnitShift']=$this->arr['objTypeUnit'][$this->result[$k]['objTypeUnitShift']];

        $this->arr=getApi3($this->conn, $pref, "requests","requestAll","id",$this->result[$k]['objRequest'], NULL, NULL, $this->domain,$this->arr);
        $this->result[$k]['objRequest']=$this->arr['requestAll']['id'][$this->result[$k]['objRequest']];

        $id_rate=$this->result[$k]['objRequest']['objRate']['id'];
        list($plan,$this->arr)=getApi2($this->conn, $pref, "payBalance","payBalanceRatePlan",array('idRate'=>$id_rate,'idShift'=>$id),NULL,NULL,NULL,NULL,$this->arr);

        if(is_null($this->result[$k]['planDtBegin'])) $this->result[$k]['planDtBegin']=$plan['plan_dt_begin'];
        if(is_null($this->result[$k]['planDtEnd'])) $this->result[$k]['planDtEnd']=$plan['plan_dt_end'];
        if(is_null($this->result[$k]['planRateHour'])) $this->result[$k]['planRateHour']=$plan['plan_rate_hour'];
        if(is_null($this->result[$k]['planRateDay'])) $this->result[$k]['planRateDay']=$plan['plan_rate_day'];
        if(is_null($this->result[$k]['planRateUnit'])) $this->result[$k]['planRateUnit']=$plan['plan_rate_unit'];
        if(is_null($this->result[$k]['planCntHour'])) $this->result[$k]['planCntHour']=$plan['plan_cnt_hour'];
        if(is_null($this->result[$k]['planCostHour'])) $this->result[$k]['planCostHour']=$plan['plan_cost_hour'];
        if(is_null($this->result[$k]['objPlanTypeUnit'])) $this->result[$k]['objPlanTypeUnit']=$plan['plan_type_unit'];

        $this->result[$k]['objPlanTypeUnit']=$this->arr['objTypeUnit'][$this->result[$k]['objPlanTypeUnit']];

        $this->result[$k]['planSum']=$plan['plan_sum'];

        //вычисляем статусы
        $sumPaid=0;
        $sumBalance=0;
        $sumExtra=0;
        unset($dtSignature);
        if(isset($this->result[$k]['arrObjBalance']))
            foreach($this->result[$k]['arrObjBalance'] as $j=>$val_b){
                if($val_b['state']==1){
                    $sumPaid=$sumPaid+$val_b['sumPaid'];
                    $sumBalance=$sumBalance+$val_b['sum']*$val_b['objType']['type'];
                    if(!is_null($val_b['dtSignature'])) $dtSignature=$val_b['dtSignature'];
                    if($val_b['objType']['primary']!=1) $sumExtra=$sumExtra+$val_b['sum']*$val_b['objType']['type'];
                }
            }
        $this->result[$k]['sumBalanceTotal']=$sumBalance;
        $this->result[$k]['sumBalanceExtra']=$sumExtra;

        $owner_close=$this->arr['owner'][$this->result[$k]['personRemove']]['dtClose'];

        if($this->result[$k]['state']==1 and !is_null($this->result[$k]['planDtBegin']) and is_null($this->result[$k]['dtBegin'])){
            $this->result[$k]['shiftStatus']="Assigned";
            $this->result[$k]['shiftStatusDescription']="1. - Исполнитель спланирован на смену, начислений еще нет";
        }elseif($this->result[$k]['state']==1 and !is_null($this->result[$k]['planDtBegin']) and !is_null($this->result[$k]['dtBegin']) and is_null($dtSignature)){
            $this->result[$k]['shiftStatus']="Priced";
            $this->result[$k]['shiftStatusDescription']="1.1. - Указан факт начала и окончания, а также начислено вознаграждение за смену.";
        }elseif($this->result[$k]['state']==1 and !is_null($this->result[$k]['planDtBegin']) and !is_null($this->result[$k]['dtBegin']) and !is_null($dtSignature) and $sumPaid==0){
            $this->result[$k]['shiftStatus']="Confirmed";
            $this->result[$k]['shiftStatusDescription']="1.1.1. - Начисленное вознаграждение за смену заверено и готово к выплате.";
        }elseif($this->result[$k]['state']==1 and !is_null($this->result[$k]['planDtBegin']) and !is_null($this->result[$k]['dtBegin']) and !is_null($dtSignature) and $sumPaid!=0 and $sumPaid==$sumBalance){
            $this->result[$k]['shiftStatus']="Paid";
            $this->result[$k]['shiftStatusDescription']="1.1.1.1. - Вознаграждение выплачено исполнителю (изменения в смене невозможны).";
        }elseif($owner_close>=$this->result[$k]['objRequest']['dtBegin'] or $this->result[$k]['objRequest']['objOrder']['objClient']['closePeriod']>=substr($this->result[$k]['objRequest']['dtBegin'],0,10)){
            $this->result[$k]['shiftStatus']="Locked";
            $this->result[$k]['shiftStatusDescription']="1.2. - Смена заблокирована от изменений (по периоду).";
        }elseif($this->result[$k]['state']==0){
            $this->result[$k]['shiftStatus']="Dismissed";
            $this->result[$k]['shiftStatusDescription']="1.3. - Исполнитель снят со смены (изменения в смене невозможны).";
        }

        $this->arr=getApi3($this->conn, $pref, "specialties","specialityAll","id",$this->result[$k]['objPlanSpeciality'], NULL, NULL, $this->domain,$this->arr);
        $this->result[$k]['objPlanSpeciality']=$this->arr['specialityAll']['id'][$this->result[$k]['objPlanSpeciality']];
    }
}
?>