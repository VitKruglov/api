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
        unset($this->postParam['input']['addtable']);

        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id'].", dt_begin=NOW(), id_person_begin=".$this->postParam['session_id']." WHERE id=".$result;

            $result2=DBExecuteNew($this->conn, $query);

            $this->result=array('result'=>'Ok', 'Id'=>$result);
        }

?>