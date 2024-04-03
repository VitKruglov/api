<?php
//-------------------------------------------------------------//
//                                                             //
//        вычисление планируемой оплаты смены по ставке    format2         //
//                                                             //
//-------------------------------------------------------------//


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    if(isset($data['search']['idRate'])){
        $id_rate=$data['search']['idRate'];
        unset($data['search']['idRate']);
    }elseif(isset($this->getParam['get']['idRate']))
        $id_rate=$this->getParam['get']['idRate'];

    if(isset($data['search']['idShift'])){
        $id_shift=$data['search']['idShift'];
        unset($data['search']['idShift']);
    }elseif(isset($this->getParam['get']['idShift']))
        $id_shift=$this->getParam['get']['idShift'];
}


list($id_owner)=explode("_",$pref);


$result=array();
if($id_rate>0 and $id_shift>0){

    list($plan_dt_begin, $plan_dt_end, $plan_cnt_hour, $plan_cost_hour, $plan_cnt, $id_request, $plan_rate_hour, $plan_rate_day, $plan_type_unit, $plan_rate_unit,$dt_begin, $dt_end)=DBQueryNew($this->conn, "SELECT s.plan_dt_begin, s.plan_dt_end, s.plan_cnt_hour, s.plan_cost_hour, s.plan_cnt, s.id_request, s.plan_rate_hour, s.plan_rate_day, s.plan_type_unit, s.plan_rate_unit, r.dt_begin, r.dt_end FROM ".$pref."orders_shifts as s, ".$pref."orders_requests as r WHERE s.id=".$id_shift." and r.id=s.id_request");

    if(is_null($plan_dt_begin) or is_null($plan_dt_end)){
        if(is_null($plan_dt_begin)) $plan_dt_begin=$dt_begin;
        if(is_null($plan_dt_end)) $plan_dt_end=$dt_end;
    }
    if(is_null($plan_cost_hour)){
        $date1 = date_create($plan_dt_begin);
        $date2 = date_create($plan_dt_end);

        $diff = date_diff($date1, $date2);
        $plan_cost_hour=$diff->format('%h');
    }
    if(is_null($plan_cnt_hour)){
        $date1 = date_create($plan_dt_begin);
        $date2 = date_create($plan_dt_end);

        $diff = date_diff($date1, $date2);
        $plan_cnt_hour=$diff->format('%h');
    } 

    if(!isset($this->arr['ratesRate'][$id_rate])){
        list($rate_hour, $hours, $rate_day, $rate_unit, $id_type_unit)=DBQueryNew($this->conn, "SELECT rate_hour, hours, rate_day, rate_unit, id_type_unit FROM ".$pref."clients_rate WHERE id=".$id_rate);
        $this->arr['ratesRate'][$id_rate]=array($rate_hour, $hours, $rate_day, $rate_unit, $id_type_unit);
    }else{
        list($rate_hour, $hours, $rate_day, $rate_unit, $id_type_unit)=$this->arr['ratesRate'][$id_rate];
    }

    if(is_null($plan_type_unit)) $plan_type_unit=$id_type_unit;
    if(is_null($plan_rate_unit)) $plan_rate_unit=$rate_unit;

    if($plan_rate_hour>0 and $plan_cost_hour>0){
        //уже есть запланированная цена за час
        $res['plan_rate_hour']=$plan_rate_hour;
        $res['plan_cost_hour']=$plan_cost_hour;   
        $res['plan_cnt_hour']=$plan_cost_hour;
        $res['plan_rate_day']=NULL;
        $res['plan_sum']=$plan_rate_hour*$plan_cost_hour;
    }elseif($plan_rate_day>0){
        //уже есть запланированная цена за смену
        $res['plan_rate_hour']=NULL;
        $res['plan_cost_hour']=NULL;   
        $res['plan_cnt_hour']=$plan_cnt_hour;   
        $res['plan_rate_day']=$plan_rate_day;
        $res['plan_sum']=$plan_rate_day;
    }elseif($rate_hour>0 and $plan_cost_hour>0){
        //есть цена за час из ставки
        $res['plan_rate_hour']=$rate_hour;
        $res['plan_cost_hour']=$plan_cost_hour;   
        $res['plan_cnt_hour']=$plan_cost_hour;
        $res['plan_rate_day']=NULL;
        $res['plan_sum']=$rate_hour*$plan_cost_hour;
    }elseif($rate_day>0){
        //есть цена за смену
        $res['plan_rate_hour']=NULL;
        $res['plan_cost_hour']=NULL;   
        $res['plan_cnt_hour']=$plan_cnt_hour;   
        $res['plan_rate_day']=$rate_day;
        $res['plan_sum']=$rate_day;
    }else{
        $res['plan_cnt_hour']=$plan_cnt_hour;
        $res['plan_cost_hour']=NULL;   
        $res['plan_rate_hour']=NULL;
        $res['plan_rate_day']=NULL;
    }
    if($plan_cnt>0 and $plan_rate_unit>0){
        $res['plan_sum']=$res['plan_sum']+$plan_cnt*$plan_rate_unit;
    }elseif($plan_cnt>0 and $rate_unit>0){
        $res['plan_sum']=$res['plan_sum']+$plan_cnt*$rate_unit;
    }
    $res['plan_type_unit']=$plan_type_unit;   
    $res['plan_rate_unit']=$plan_rate_unit;   
    $res['plan_dt_begin']=$plan_dt_begin;   
    $res['plan_dt_end']=$plan_dt_end;   

/*
    if($rate_day>0 and $cost_hour>=$hours){ //для оплаты за целый день и более
        $cnt_day=floor($cost_hour/$hours);
        $summ=$cnt_day*$rate_day+($cost_hour-$cnt_day*$hours)*$rate_hour;
    }else{
        $summ=$rate_hour*$cost_hour;
    }
    //учитываем стоимость штук (коробок и т.п.)
    if($rate_unit>0 and $cnt>0) 
        $summ=$summ+$rate_unit*$cnt;
    */

    $this->result=array('0'=>array($res,$this->arr));
}else{
    $this->result=array('0'=>"Error: 404");
}

?>