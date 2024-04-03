<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание нового платежа                   //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
    $lrv=$this->postParam['lrv'];
}

$result=array();

list($primary, $name_operation)=DBQueryNew($this->conn, "SELECT ".$pref."pay_type.primary,name_operation FROM ".$pref."pay_type WHERE id=".$this->postParam['input']['idType']);

if($primary==1 and $lrv!=1){
    $this->result=array('result'=>'This operation is not supported');
}else{

    if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);
    if(isset($this->postParam['input']['idPersonSignature'])) unset($this->postParam['input']['idPersonSignature']);

    if(isset($this->postParam['input']['idShift'])){
        $this->postParam['input']['idOperation']=$this->postParam['input']['idShift'];
        $this->postParam['input']['nameOperation']='orders_shifts';
        unset($this->postParam['input']['idShift']);
    } 
        
    //---------------------общая часть для всех ADD в отдельном файле---------------//
    include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
    //----------------------в нем формируется query --------------------------------//

    if($result['error']){
        $this->result=array('error'=>$result['error'],'query'=>$query);
    }else{
        $result=DBExecuteNew($this->conn, $query);

        $query="UPDATE ".$pref.$this->table_name." SET dt=NOW(), dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$result;
    
        $result2=DBExecuteNew($this->conn, $query);
    
        $this->result=array('result'=>'Ok', 'Id'=>$result, 'name_operation'=>$name_operation);
    }
}
?>