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

        unset($this->postParam['input']['id']);
        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);

        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//
        if($this->postParam['input']['gender']!=NULL and $this->postParam['input']['gender']!='male' and $this->postParam['input']['gender']!='female')
            $result['error']="400 Gender is not male/female";
        else
            $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query2="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;
            $result2=DBExecuteNew($this->conn, $query2);

          //меняем координаты 
            list($id_fias)=DBQueryNew($this->conn, "SELECT id_fias FROM ".$pref.$this->table_name." WHERE id=$id");
            list($geo_lat,$geo_lon)=DBQueryNew($this->conn, "SELECT geo_lat, geo_lon FROM ".$pref."fias WHERE id='$id_fias'");

            $query="UPDATE ".$pref.$this->table_name." SET geo_lat='$geo_lat', geo_lon='$geo_lon' WHERE id=".$id;
            $result2=DBExecuteNew($this->conn, $query);

            $this->result=array('result'=>'Ok'); //, 'arr'=>$arr
        }
    }else{
        $this->result=array('message'=>404);
    }
}
?>