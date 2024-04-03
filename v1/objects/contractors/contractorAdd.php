<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание нового контрагента                   //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

    if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);
    
    if(isset($this->postParam['input']['inn']))
        $this->postParam['input']['inn']=preg_replace('/[^0-9]/', "", $this->postParam['input']['inn']);
    if(strlen($this->postParam['input']['inn'])!=12 and strlen($this->postParam['input']['inn'])!=10)
        unset($this->postParam['input']['inn']);

    if(isset($this->postParam['input']['kpp']))
        $this->postParam['input']['kpp']=preg_replace('/[^0-9]/', "", $this->postParam['input']['kpp']);
    if(strlen($this->postParam['input']['kpp'])!=9)
        unset($this->postParam['input']['kpp']);

    if(isset($this->postParam['input']['ogrn']))
        $this->postParam['input']['ogrn']=preg_replace('/[^0-9]/', "", $this->postParam['input']['ogrn']);
    if(strlen($this->postParam['input']['ogrn'])!=13)
        unset($this->postParam['input']['ogrn']);

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