<?php
//-------------------------------------------------------------//
//                                                             //
//        удаление клиента по ID                             //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(isset($data['session_id']))
        $session_id=$data['session_id'];
}

$result=array();

if (count($this->urlData)==1){
    $id=$this->urlData[0];
    if($id>0){
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id=".$id;

        $result=DBQueryNew($this->conn, $query);

        if($result['error'])
            $this->result=array('error'=>$result['error'],'query'=>$query);
        elseif(count($result)>0){
            //проверяем , были ли начисления на счет и проверяем права Корректор
            list($pay_exist)=DBQueryNew($this->conn, "SELECT id FROM ".$pref."pay_pay WHERE state=1 and id_lp_account=".$id);
            list($permis_del_account)=DBQueryNew($this->conn, "SELECT id_unit FROM ".$pref."persons WHERE id=".$session_id);

            if(is_null($pay_exist) or $permis_del_account==1){
                $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$session_id.", state=0 WHERE id=".$id;
                $result=DBExecuteNew($this->conn, $query);

                if($result['error']){
                    $this->result=array('error'=>$result['error'],'query'=>$query);
                }else{
                    $this->result=array('result'=>'Ok', 'Id'=>$id);
                }
            }else{
                if($pay_exist>0)
                    $this->result=array($result['error']="400 Account cannot be deleted");
                elseif($permis_del_account!=1)
                    $this->result=array($result['error']="403 You don't have permission to delete account");
            }
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>