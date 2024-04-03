<?php
//-------------------------------------------------------------//
//                                                             //
//        изменение информации о выплате по ID                 //
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
        if(isset($this->postParam['input']['idPersonSignature'])) unset($this->postParam['input']['idPersonSignature']);
        if(isset($this->postParam['input']['idPersonPay'])) unset($this->postParam['input']['idPersonPay']);

        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//
        
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