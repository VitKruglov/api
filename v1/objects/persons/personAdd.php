<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание нового клиента                   //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
    list($ak)=explode("_",$pref);
}

$result=array();


        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
        #    DBExecuteNew($this->conn, "UPDATE ".$pref.$this->table_name." SET id_owner=$ak WHERE id=$result");
            $this->result=array('result'=>'Ok', 'Id'=>$result);
        }

?>