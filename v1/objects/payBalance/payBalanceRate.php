<?php
//-------------------------------------------------------------//
//                                                             //
//        вычисление оплаты смены по ставке    format2         //
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
    list($cost_hour, $cnt, $cnt_hour)=DBQueryNew($this->conn, "SELECT cost_hour,cnt, cnt_hour FROM ".$pref."orders_shifts WHERE id=".$id_shift);

    list($rate_hour, $hours, $rate_day, $rate_unit)=DBQueryNew($this->conn, "SELECT rate_hour, hours, rate_day, rate_unit FROM ".$pref."clients_rate WHERE id=".$id_rate);
/*
    if($rate_day>0 and $cost_hour>=$hours){ //для оплаты за целый день и более
        $cnt_day=floor($cost_hour/$hours);
        $summ=$cnt_day*$rate_day+($cost_hour-$cnt_day*$hours)*$rate_hour;
    }else{
        $summ=$rate_hour*$cost_hour;
    }
    */

    if($rate_hour>0 and $cost_hour>0){ //для почасовой
        $summ=$rate_hour*$cost_hour;
    }elseif($cnt_hour>0){
        $summ=$rate_day;   
    }
    //учитываем стоимость штук (коробок и т.п.)
    if($rate_unit>0 and $cnt>0) 
        $summ=$summ+$rate_unit*$cnt;

    $this->result=array('0'=>$summ);
}else{
    $this->result=array('0'=>"Error: 404");
}

?>