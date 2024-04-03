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
        $this->result=array('id'=>$id);

        unset($this->postParam['input']['id']);
        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);
        if(isset($this->postParam['input']['idPersonSignature'])) unset($this->postParam['input']['idPersonSignature']);

        list($name_operation_old)=DBQueryNew($this->conn, "SELECT name_operation FROM ".$pref."pay_balance as b WHERE b.id=$id");
            
        if($this->postParam['input']['idType']>0){
            list($name_operation)=DBQueryNew($this->conn, "SELECT t.name_operation FROM ".$pref."pay_type as t WHERE t.id='".$this->postParam['input']['idType']."'");

            if($name_operation!=$name_operation_old){
                if(!$name_operation)
                    DBExecuteNew($this->conn, "UPDATE ".$pref."pay_balance SET id_operation=NULL, name_operation=NULL WHERE id=$id");
                else
                    DBExecuteNew($this->conn, "UPDATE ".$pref."pay_balance SET id_operation=NULL, name_operation='$name_operation' WHERE id=$id");
            }
        }
        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//

        
        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;
            $result2=DBExecuteNew($this->conn, $query);

            $this->result=array('result'=>'Ok'); //, 'arr'=>$arr
        }
    }else{
        $this->result=array('message'=>404);
    }
}
?>