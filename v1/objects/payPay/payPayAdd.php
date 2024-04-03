<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание новой выплаты                  //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

        if(isset($this->postParam['input']['idPersonSignature'])) unset($this->postParam['input']['idPersonSignature']);
        if(isset($this->postParam['input']['idPersonPay'])) unset($this->postParam['input']['idPersonPay']);

        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            

            $query="UPDATE ".$pref.$this->table_name." SET dt_pay=NOW(), id_person_pay=".$this->postParam['session_id']." WHERE id=".$result;

            $result2=DBExecuteNew($this->conn, $query);

            //генерируем операцию
            if($this->postParam['type_balance']>0){
                list($id_worker)=DBQueryNew($this->conn, "SELECT w.id FROM ".$pref."lp_worker as w, ".$pref."lp_account as a WHERE a.id=".$this->postParam['input']['id_lp_account']." and w.id_man=a.id_man");

                $query="INSERT INTO ".$pref."pay_balance (`dt`, `state`, `id_type`, `id_worker`, `id_operation`, `name_operation`, `sum`, `note`, `dt_change`, `id_person_change`, `sum2` ) VALUE (NOW(), 1, ".$this->postParam['type_balance']." , $id_worker, $result, 'pay_pay', ".$this->postParam['input']['sum'].", '".$this->postParam['input']['note']."', NOW(), ".$this->postParam['session_id'].",".$this->postParam['input']['sum'].")";

                $result2=DBExecuteNew($this->conn, $query);
            }

            $this->result=array('result'=>'Ok', 'Id'=>$result,'query'=>$query);
        }

?>