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

        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);
        if(isset($this->postParam['input']['idPersonClose'])) unset($this->postParam['input']['idPersonClose']);
        if(isset($this->postParam['input']['idPersonClosePeriod'])) unset($this->postParam['input']['idPersonClosePeriod']);

        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query2="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$result;
            $result2=DBExecuteNew($this->conn, $query2);

            $this->result=array('result'=>'Ok', 'Id'=>$result);
        }

?>