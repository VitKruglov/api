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