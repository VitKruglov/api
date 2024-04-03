<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание нового физ.лица                  //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);

        //---------------------общая часть для всех Add в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//
        if($this->postParam['input']['gender']!=NULL and $this->postParam['input']['gender']!='male' and $this->postParam['input']['gender']!='female')
            $result['error']="400 Gender is not male/female";
        else
            $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query2="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$result;
            $result2=DBExecuteNew($this->conn, $query2);
            
            $this->result=array('result'=>'Ok', 'Id'=>$result);
        }

?>