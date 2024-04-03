<?php
//-------------------------------------------------------------//
//                                                             //
//        утверждение начислений                                //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();
unset($query);

if(isset($this->postParam['input']['id']) or isset($this->postParam['input']['idShift'])){
    $id=$this->postParam['input']['id'];
    if($id>0){
        $this->result=array('id'=>$id);
        list($id_operation, $name_operation)=DBQueryNew($this->conn, "SELECT id_operation, name_operation FROM ".$pref.$this->table_name." WHERE id=$id");
        unset($this->postParam['input']['id']);
    }elseif($this->postParam['input']['idShift']>0){
        $id_operation=$this->postParam['input']['idShift'];
        $name_operation='orders_shifts';
        unset($this->postParam['input']['idShift']);
    }else{
        $this->result=array('message'=>404);
    }

    if($this->postParam['input']['signature']==1){
        unset($this->postParam['input']['signature']);

        $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id'].",dt_signature=NOW(), id_person_signature=".$this->postParam['session_id']." WHERE id_operation=$id_operation and name_operation='$name_operation' and state=1";
    }elseif($this->postParam['input']['signature']==0){
        unset($this->postParam['input']['signature']);

        $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id'].",dt_signature=NULL, id_person_signature=NULL WHERE id_operation=$id_operation and name_operation='$name_operation' and state=1";
    }else{
        $this->result=array('message'=>404);
    }

    if($query){
        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $this->result=array('result'=>'Ok'); //, 'arr'=>$arr
        }
    }
}
?>