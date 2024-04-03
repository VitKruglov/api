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
        if(isset($this->postParam['input']['idPersonBegin'])) unset($this->postParam['input']['idPersonBegin']);
        if(isset($this->postParam['input']['idPersonClose'])) unset($this->postParam['input']['idPersonClose']);

        //---------------------общая часть для всех PUT в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllPut.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);

        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id']." WHERE id=".$id;

            $result2=DBExecuteNew($this->conn, $query);
            
            $this->result=array('result'=>'Ok'); //, 'arr'=>$arr

            //копируем данные таблиц (если не скопировались при создании АК)
            $pref_ak=$id."_";
            $db_ak = DBConnect($tmpl, $this->config, $pref_ak);

            //копируем роли
            $result3=DBFetchNew($this->conn,"SELECT name, script_name, note FROM unit_name_like ORDER BY id");
            foreach($result3 as $i => $val){
                DBExecuteNew($db_ak, "INSERT INTO ".$id."_unit_name (name, script_name, note) VALUE ('".$val[0]."','".$val[1]."','".$val[2]."')");
            }     
            //копируем расширенные требования
            $result3=DBFetchNew($this->conn,"SELECT clients, name, type, hidden, required, note FROM global_requirements ORDER BY id");
            foreach($result3 as $i => $val){
                DBExecuteNew($db_ak, "INSERT INTO ".$id."_global_requirements (clients, name, type, hidden, required, note) VALUE ('".$val[0]."','".$val[1]."','".$val[2]."','".$val[3]."','".$val[4]."','".$val[5]."')");
            }  
        }
        
        $this->result=array('result'=>'Ok');
    }else{
        $this->result=array('message'=>404);
    }
}
?>