<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание нового клиента                   //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

        if(isset($this->postParam['input']['id_person_change'])) unset($this->postParam['input']['id_person_change']);
        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);
        if(isset($this->postParam['input']['id_person_plan'])) unset($this->postParam['input']['id_person_plan']);
        if(isset($this->postParam['input']['idPersonPlan'])) unset($this->postParam['input']['idPersonPlan']);
        if(isset($this->postParam['input']['id_person_remove'])) unset($this->postParam['input']['id_person_remove']);
        if(isset($this->postParam['input']['idPersonRemove'])) unset($this->postParam['input']['idPersonRemove']);

        //на всякий случай удаляем планируемые данные
        if(isset($this->postParam['input']['plan_dt_begin'])) unset($this->postParam['input']['plan_dt_begin']);    #
        if(isset($this->postParam['input']['planDtBegin'])) unset($this->postParam['input']['planDtBegin']);        #
        if(isset($this->postParam['input']['plan_dt_end'])) unset($this->postParam['input']['plan_dt_end']);        #
        if(isset($this->postParam['input']['planDtEnd'])) unset($this->postParam['input']['planDtEnd']);            #
        if(isset($this->postParam['input']['plan_cnt_hour'])) unset($this->postParam['input']['plan_cnt_hour']);    #
        if(isset($this->postParam['input']['planCntHour'])) unset($this->postParam['input']['planCntHour']);        #
        if(isset($this->postParam['input']['plan_cost_hour'])) unset($this->postParam['input']['plan_cost_hour']);  #
        if(isset($this->postParam['input']['planCostHour'])) unset($this->postParam['input']['planCostHour']);      #
        if(isset($this->postParam['input']['plan_rate_hour'])) unset($this->postParam['input']['plan_rate_hour']);  #
        if(isset($this->postParam['input']['planRateHour'])) unset($this->postParam['input']['planRateHour']);      #
        if(isset($this->postParam['input']['plan_rate_day'])) unset($this->postParam['input']['plan_rate_day']);    #
        if(isset($this->postParam['input']['planRateDay'])) unset($this->postParam['input']['planRateDay']);        #
        if(isset($this->postParam['input']['plan_rate_unit'])) unset($this->postParam['input']['plan_rate_unit']);  #
        if(isset($this->postParam['input']['planRateUnit'])) unset($this->postParam['input']['planRateUnit']);      #
        if(isset($this->postParam['input']['plan_type_unit'])) unset($this->postParam['input']['plan_type_unit']);  #
        if(isset($this->postParam['input']['planTypeUnit'])) unset($this->postParam['input']['planTypeUnit']);      #

        //подставляем планируемые данные из ставки и заявки
        if($this->postParam['input']['idRequest']>0){
            list($id_rate,$this->postParam['input']['planDtBegin'],$this->postParam['input']['planDtEnd'])=DBQueryNew($this->conn, "SELECT id_rate, dt_begin, dt_end FROM ".$pref."orders_requests WHERE id=".$this->postParam['input']['idRequest']);

            $date1 = date_create($this->postParam['input']['planDtBegin']);
            $date2 = date_create($this->postParam['input']['planDtEnd']);
            $diff = date_diff($date1, $date2);
            $this->postParam['input']['planCntHour']=$diff->format('%h');

            list($this->postParam['input']['planRateHour'], $this->postParam['input']['planCostHour'], $this->postParam['input']['planRateDay'], $this->postParam['input']['planRateUnit'], $this->postParam['input']['planTypeUnit'])=DBQueryNew($this->conn, "SELECT rate_hour, hours, rate_day, rate_unit, id_type_unit FROM ".$pref."clients_rate WHERE id=".$id_rate);
        }

        
        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        //проверка на входящие данные
        //соответствие Заказа и объекта
        if($this->postParam['input']['idRequest']>0 and $this->postParam['input']['idContract']>0){
            list($id_shift_exist)=DBQueryNew($this->conn, "SELECT id FROM ".$pref."orders_shifts WHERE id_request=".$this->postParam['input']['idRequest']." and id_contract=".$this->postParam['input']['idContract']." and state=1");
            if($id_shift_exist>0)
                $result['error']="400 A shift with idContract=".$this->postParam['input']['idContract']." is already exists";
            else
                $prov_contract=true;
        }else
            $result['error']="400 Not found Request or Contract";

        if($prov_contract)
            $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query="UPDATE ".$pref.$this->table_name." SET dt_plan=NOW(), id_person_plan=".$this->postParam['session_id'].", dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$result;
            $result2=DBExecuteNew($this->conn, $query);

            list($id_request, $id_speciality)=DBQueryNew($this->conn, "SELECT id_request, id_speciality FROM ".$pref.$this->table_name." WHERE id=$result");
            if($id_request>0 and is_null($id_speciality)){
                list($id_speciality)=DBQueryNew($this->conn, "SELECT r1.id_speciality FROM ".$pref."orders_requests as r, ".$pref."clients_rate as r1 WHERE r.id=$id_request and r1.id=r.id_rate");

                $query="UPDATE ".$pref.$this->table_name." SET id_speciality=$id_speciality WHERE id=".$result;
                $result2=DBExecuteNew($this->conn, $query);
            }
            
            $this->result=array('result'=>'Ok', 'Id'=>$result);
        }

?>