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
    if($id>0){
        $this->result=array('id'=>$id);

        list($close_old, $state_old)=DBQueryNew($this->conn, "SELECT close_period,state FROM ".$pref.$this->table_name." WHERE id=".$id);

        unset($this->postParam['input']['id']);
        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);
        if(isset($this->postParam['input']['idPersonClose'])) unset($this->postParam['input']['idPersonClose']);
        if(isset($this->postParam['input']['idPersonClosePeriod'])) unset($this->postParam['input']['idPersonClosePeriod']);

        if(isset($this->postParam['input']['closePeriod']) and $this->postParam['input']['closePeriod']!=$close_old){
            DBExecuteNew($this->conn, "UPDATE ".$pref.$this->table_name." SET dt_close_period=NOW(), id_person_close_period=".$this->postParam['session_id']." WHERE id=".$id);
        }
        if(isset($this->postParam['input']['state']) and $this->postParam['input']['state']!=$state_old and $state_old==1){
            DBExecuteNew($this->conn, "UPDATE ".$pref.$this->table_name." SET dt_close=NOW(), id_person_close=".$this->postParam['session_id']." WHERE id=".$id);
        }

        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            //меняем координаты 
            list($id_fias)=DBQueryNew($this->conn, "SELECT id_fias FROM ".$pref.$this->table_name." WHERE id=$id");
            list($geo_lat,$geo_lon)=DBQueryNew($this->conn, "SELECT geo_lat, geo_lon FROM ".$pref."fias WHERE id='$id_fias'");

            $query="UPDATE ".$pref.$this->table_name." SET geo_lat='$geo_lat', geo_lon='$geo_lon' WHERE id=".$id;
            $result2=DBExecuteNew($this->conn, $query);

            $query2="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;
            $result2=DBExecuteNew($this->conn, $query2);
            
            $this->result=array('result'=>'ok'); //, 'arr'=>$arr

        }
    }else{
        $this->result=array('message'=>404);
    }
}
?>