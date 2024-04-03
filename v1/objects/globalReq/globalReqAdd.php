<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание новых базовых требований         //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();


        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $this->result=array('result'=>'Ok', 'Id'=>$result);
        }

?>