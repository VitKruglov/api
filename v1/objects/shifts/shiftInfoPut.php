<?php
//-------------------------------------------------------------//
//                                                             //
//        изменение информации о клиенте по ID                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

if(isset($this->postParam['input']['id'])){
    $id=$this->postParam['input']['id'];
    if($id>0){
        list($state_old)=DBQueryNew($this->conn, "SELECT state FROM ".$pref.$this->table_name." WHERE id=$id");
        $this->result=array('id'=>$id);

        unset($this->postParam['input']['id']);
        if(isset($this->postParam['input']['id_person_change'])) unset($this->postParam['input']['id_person_change']);
        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);
        if(isset($this->postParam['input']['id_person_plan'])) unset($this->postParam['input']['id_person_plan']);
        if(isset($this->postParam['input']['idPersonPlan'])) unset($this->postParam['input']['idPersonPlan']);
    #    if(isset($this->postParam['input']['id_person_remove'])) unset($this->postParam['input']['id_person_remove']);
    #    if(isset($this->postParam['input']['idPersonRemove'])) unset($this->postParam['input']['idPersonRemove']);

        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//

        //проверка на соответствие Заказа и объекта
        if(isset($this->postParam['input']['idRequest']) or isset($this->postParam['input']['idContract'])){
            if($this->postParam['input']['idRequest']>0 and $this->postParam['input']['idContract']>0){
                list($id_shift_exist)=DBQueryNew($this->conn, "SELECT id FROM ".$pref."orders_shifts WHERE id_request=".$this->postParam['input']['idRequest']." and id_contract=".$this->postParam['input']['idContract']." and state=1 and id!=$id");
                if($id_shift_exist>0)
                    $result['error']="400 A shift with idContract=".$this->postParam['input']['idContract']." is already exists";
                else
                    $prov_contract=true;
            }else
                $result['error']="400 Not found Request or Contract";

            if($prov_contract)
                $result=DBExecuteNew($this->conn, $query);
        }else
            $result=DBExecuteNew($this->conn, $query);


        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            if($this->postParam['input']['state']==0 and $state_old==1)
                $query2="UPDATE ".$pref.$this->table_name." SET dt_remove=NOW(), id_person_remove=".$this->postParam['session_id'].", dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;
            else
                $query2="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;

            $result2=DBExecuteNew($this->conn, $query2);

            list($id_request, $id_speciality, $id_contract)=DBQueryNew($this->conn, "SELECT id_request, id_speciality, id_contract FROM ".$pref.$this->table_name." WHERE id=$id");
            if($id_request>0 and is_null($id_speciality)){
                list($id_speciality)=DBQueryNew($this->conn, "SELECT r1.id_speciality FROM ".$pref."orders_requests as r, ".$pref."clients_rate as r1 WHERE r.id=$id_request and r1.id=r.id_rate");

                $query2="UPDATE ".$pref.$this->table_name." SET id_speciality=$id_speciality WHERE id=".$id;
                $result2=DBExecuteNew($this->conn, $query2);
            }

            //делаем начисление
            list($id_rate)=DBQueryNew($this->conn, "SELECT id_rate FROM ".$pref."orders_requests WHERE id=$id_request");
            $arr_sum=getApi2($this->conn, $pref, "payBalance","payBalanceRateShift",'idShift',$id);
            

            if($this->postParam['input']['planRateUnit']=='NULL' or !isset($this->postParam['input']['planRateUnit'])) $this->postParam['input']['planRateUnit']=0;
            if($this->postParam['input']['planRateDay']=='NULL' or !isset($this->postParam['input']['planRateDay'])) $this->postParam['input']['planRateDay']=0;
            if($this->postParam['input']['planRateHour']=='NULL' or !isset($this->postParam['input']['planRateHour'])) $this->postParam['input']['planRateHour']=0;
            if($this->postParam['input']['costHour']=='NULL' or !isset($this->postParam['input']['costHour'])) $this->postParam['input']['costHour']=0;
            if($this->postParam['input']['cnt']=='NULL' or !isset($this->postParam['input']['cnt'])) $this->postParam['input']['cnt']=0;
            if($this->postParam['input']['idTypeUnit']=='NULL' or !isset($this->postParam['input']['idTypeUnit'])) $this->postParam['input']['idTypeUnit']=0;
            if($this->postParam['input']['sumUnit']=='NULL' or !isset($this->postParam['input']['sumUnit'])){
                $this->postParam['input']['sumUnit']=$arr_sum['sum_unit'];
                $result2=DBExecuteNew($this->conn, "UPDATE ".$pref.$this->table_name." SET sum_unit=".$arr_sum['sum_unit']." WHERE id=".$id);
            }
            if($this->postParam['input']['sum']=='NULL' or !isset($this->postParam['input']['sum'])){
                $this->postParam['input']['sum']=$arr_sum['sum_hour_day'];
                $result2=DBExecuteNew($this->conn, "UPDATE ".$pref.$this->table_name." SET sum=".$arr_sum['sum_hour_day']." WHERE id=".$id);
            }

            $query_pay="INSERT INTO ".$pref."pay_balance SET `dt`='".date("Y-m-d H:i:s",time())."', `dt_change`='".date("Y-m-d H:i:s",time())."', `id_person_change`=".$this->postParam['session_id'].", `state`=1, `id_type`=1, `id_contract`=$id_contract, `id_operation`=$id, `name_operation`='orders_shifts', `id_rate`=$id_rate, `sum`=".$arr_sum['sum'].", `rate_unit`=".$this->postParam['input']['planRateUnit'].", `rate_day`=".$this->postParam['input']['planRateDay'].", `rate_hour`=".$this->postParam['input']['planRateHour'].", `hour`=".$this->postParam['input']['costHour'].", `cnt`=".$this->postParam['input']['cnt'].", `id_type_unit`=".$this->postParam['input']['idTypeUnit'].", `sum_unit`=".$arr_sum['sum_unit'].",`note`='".$this->postParam['input']['note']."' ON DUPLICATE KEY UPDATE state=state";
            $id_pay_balance=DBExecuteNew($this->conn, $query_pay);

            if($id_pay_balance>0){
                //меняем статус state на 0 для всех старых начислений
                DBExecuteNew($this->conn, "UPDATE ".$pref."pay_balance SET `dt_change`='".date("Y-m-d H:i:s",time())."', `id_person_change`=".$this->postParam['session_id'].", `state`=0 WHERE `id_type`=1 and `id_contract`=$id_contract and `id_operation`=$id and `name_operation`='orders_shifts' and `id_rate`=$id_rate and `id`!=".$id_pay_balance);
            }

            $this->result=array('result'=>'Ok','idPayBalance'=>$id_pay_balance); //, 'arr'=>$arr
        }
    }else{
        $this->result=array('message'=>404);
    }
}
?>