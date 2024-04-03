<?php
//-------------------------------------------------------------//
//                                                             //
//        запланировано смена                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

if(isset($this->postParam['input']['id'])){
    $id=$this->postParam['input']['id'];
    if($id>0){
        $this->result=array('id'=>$id);

        unset($this->postParam['input']['id']);

        $query="UPDATE ".$pref.$this->table_name." SET dt_plan=NOW(), id_person_plan=".$this->postParam['session_id'].", dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;
           
        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $this->result=array('result'=>'Ok'); //, 'arr'=>$arr
        }
    }else{
        $this->result=array('message'=>404);
    }
}
?>