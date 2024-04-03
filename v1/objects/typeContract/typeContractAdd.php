<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание нового типа договоров            //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

        if(isset($this->postParam['input']['id_person_change'])) unset($this->postParam['input']['id_person_change']);

        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$result;
            $result2=DBExecuteNew($this->conn, $query);
            
            $this->result=array('result'=>'Ok', 'Id'=>$result);
        }

?>