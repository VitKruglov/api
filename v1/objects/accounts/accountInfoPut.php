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
    if(isset($this->postParam['input']['bankBik']))
        $this->postParam['input']['bankBik']=change_int($this->postParam['input']['bankBik'],9); 
    if(isset($this->postParam['input']['card']))
        $this->postParam['input']['card']=change_int($this->postParam['input']['card'],16);
    if(isset($this->postParam['input']['account']))
        $this->postParam['input']['account']=change_int($this->postParam['input']['account'],30);
    
    if($this->postParam['input']['type']==0 and (!is_null($this->postParam['input']['account']) or !is_null($this->postParam['input']['card']) or !is_null($this->postParam['input']['bankName']) or !is_null($this->postParam['input']['bankBik'])))
        $this->result=array($result['error']="400 Data format error");

    elseif($id>0){
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
            $query2="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;
            $result2=DBExecuteNew($this->conn, $query2);
            
            $this->result=array('result'=>'Ok'); //, 'arr'=>$arr
        }
    }else{
        $this->result=array('message'=>404);
    }
}
?>