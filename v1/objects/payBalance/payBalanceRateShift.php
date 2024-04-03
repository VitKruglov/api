<?php
//-------------------------------------------------------------//
//                                                             //
//        вычисление суммы оплаты из полей смены по фактически введенным данным менеджером    format2         //
//                                                             //
//-------------------------------------------------------------//


if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['sort']))
        $sort=$data['sort'];

    if(isset($data['search']['idShift'])){
        $id_shift=$data['search']['idShift'];
        unset($data['search']['idShift']);
    }elseif(isset($this->getParam['get']['idShift']))
        $id_shift=$this->getParam['get']['idShift'];
}


list($id_owner)=explode("_",$pref);


$result=array();
if($id_shift>0){

    list($cost_hour, $cnt, $dt_begin, $dt_end, $plan_cnt_hour, $plan_cost_hour, $plan_cnt, $id_request, $plan_rate_hour, $plan_rate_day, $plan_type_unit, $plan_rate_unit, $sum, $sum_unit)=DBQueryNew($this->conn, "SELECT  cost_hour,cnt, dt_begin, dt_end, plan_cnt_hour, plan_cost_hour,plan_cnt, id_request, plan_rate_hour, plan_rate_day, plan_type_unit, plan_rate_unit, sum, sum_unit FROM ".$pref."orders_shifts WHERE id=".$id_shift);

    if(is_null($cost_hour)){//если нет оплачиваемых часов, вычисляем из начала и конца смены
        $date1 = date_create($dt_begin);
        $date2 = date_create($dt_end);

        $diff = date_diff($date1, $date2);
        $cost_hour=$diff->format('%h');
    }

    if(is_null($sum)){
        if($plan_rate_hour>0 and $cost_hour>0){ //для почасовой
            $summ=$plan_rate_hour*$cost_hour;
        }elseif($plan_rate_day>0){
            $summ=$plan_rate_day;   
        }
    }else
        $summ=$sum;

    //учитываем стоимость штук (коробок и т.п.)
    if($sum_unit>0)
        $sum_unit=$sum_unit;
    elseif(is_null($sum_unit) and $plan_rate_unit>0 and $cnt>0) 
        $sum_unit=$plan_rate_unit*$cnt;
    else
        $sum_unit=0;
 #   $this->result=array('0'=>$summ);
    $this->result=array('sum_hour_day'=>$summ, 'sum_unit'=>$sum_unit, 'sum'=>$summ+$sum_unit);

}else{
    $this->result=array('0'=>"Error: 404");
}

?>