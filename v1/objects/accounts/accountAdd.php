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

        if(isset($this->postParam['input']['bankBik']))
            $this->postParam['input']['bankBik']=change_int($this->postParam['input']['bankBik'],9); 
        if(isset($this->postParam['input']['card']))
            $this->postParam['input']['card']=change_int($this->postParam['input']['card'],16);
        if(isset($this->postParam['input']['account']))
            $this->postParam['input']['account']=change_int($this->postParam['input']['account'],30);
        
        //---------------------общая часть для всех Add в отдельном файле---------------//
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