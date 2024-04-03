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
    $state_new=$this->postParam['input']['state'];

    if(isset($this->postParam['input']['bankBik']))
        $this->postParam['input']['bankBik']=change_int($this->postParam['input']['bankBik'],9); 
    if(isset($this->postParam['input']['card']))
        $this->postParam['input']['card']=change_int($this->postParam['input']['card'],16);
    if(isset($this->postParam['input']['account']))
        $this->postParam['input']['account']=change_int($this->postParam['input']['account'],30);

    if($this->postParam['input']['type']==0 and (!is_null($this->postParam['input']['account']) or !is_null($this->postParam['input']['card']) or !is_null($this->postParam['input']['bankName']) or !is_null($this->postParam['input']['bankBik'])))
        $this->result=array($result['error']="400 Data format error");

    elseif($id>0){
        $this->result=array('id'=>$id);

        unset($this->postParam['input']['id']);
        unset($this->postParam['input']['state']);

        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);

        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//
        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query2="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;
            $result2=DBExecuteNew($this->conn, $query2);
            
            $this->result=array('result'=>'Ok'); //, 'arr'=>$arr

            //проверяем на изменение статуса, были ли начисления на счет и проверяем права Корректор
            list($state_old)=DBQueryNew($this->conn, "SELECT state FROM ".$pref.$this->table_name." WHERE id=".$id);
            if($state_new==1)
                DBExecuteNew($this->conn, "UPDATE ".$pref.$this->table_name." SET state=1 WHERE id=".$id);
            elseif($state_new==0 and $state_old==1){
                list($pay_exist)=DBQueryNew($this->conn, "SELECT id FROM ".$pref."pay_pay WHERE state=1 and id_lp_account=".$id);
                list($permis_del_account)=DBQueryNew($this->conn, "SELECT id_unit FROM ".$pref."persons WHERE id=".$this->postParam['session_id']);

                if(is_null($pay_exist) or $permis_del_account==1){
                    DBExecuteNew($this->conn, "UPDATE ".$pref.$this->table_name." SET state=0 WHERE id=".$id);
                }else{
                    if($pay_exist>0)
                        $this->result=array($result['error']="400 Account cannot be deleted");
                    elseif($permis_del_account!=1)
                        $this->result=array($result['error']="403 You don't have permission to delete account");
                }
            }
        }
    }else{
        $this->result=array('message'=>404);
    }
}
?>