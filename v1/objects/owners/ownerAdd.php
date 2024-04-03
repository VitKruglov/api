<?php
//-------------------------------------------------------------//
//                                                             //
//                   создание новой АК                   //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

        if(isset($this->postParam['input']['id_person_change'])) unset($this->postParam['input']['id_person_change']);
        if(isset($this->postParam['input']['idPersonChange'])) unset($this->postParam['input']['idPersonChange']);
        if(isset($this->postParam['input']['idPersonBegin'])) unset($this->postParam['input']['idPersonBegin']);
        if(isset($this->postParam['input']['idPersonClose'])) unset($this->postParam['input']['idPersonClose']);

        //---------------------общая часть для всех ADD в отдельном файле---------------//
        include_once $GLOBALS['ver'].'/objects/objectFileAllAdd.php';
        //----------------------в нем формируется query --------------------------------//

        $result=DBExecuteNew($this->conn, $query);
        
        if($result['error']){
            $this->result=array('error'=>$result['error'],'query'=>$query);
        }else{
            $id=$result;
            $query="UPDATE ".$pref.$this->table_name." SET dt_change=NOW(), id_person_change=".$this->postParam['session_id'].", dt_begin=NOW(), id_person_begin=".$this->postParam['session_id']." WHERE id=".$result;

            $result2=DBExecuteNew($this->conn, $query);

            //создаем таблицы
            $pref_ak=$id."_";
            $db_ak = DBConnect($tmpl, $this->config, $pref_ak);

            $result3=DBFetchNew($this->conn,"SELECT name_table, copy_data FROM modules_tables");
            foreach($result3 as $i => $val){
                $name_table=$val[0];
                $copy_data=$val[1];

                $arr_tbl_main=DBQueryNew($this->conn, "SHOW CREATE TABLE ".$name_table);	
                DBExecuteNew($db_ak,str_replace("`".$name_table."`", "`".$pref_ak.$name_table."`", $arr_tbl_main[1]));
                $s.="Создана таблица ".$pref_ak.$name_table."<br>"; 
                if($copy_data=='y'){
                    //копируем расширенные требования
                    DBExecuteNew($db_ak, "ALTER TABLE ".$pref_ak.$name_table." AUTO_INCREMENT = 1");         
                    DBExecuteNew($db_ak, "INSERT INTO ".$pref_ak.$name_table." SELECT * FROM ".$name_table);       
                } 
            }
            //копируем роли
            $result3=DBFetchNew($this->conn,"SELECT name, script_name, note FROM unit_name_like ORDER BY id");
            foreach($result3 as $i => $val){
                DBExecuteNew($db_ak, "INSERT INTO ".$id."_unit_name (name, script_name, note) VALUE ('".$val[0]."','".$val[1]."','".$val[2]."')");
            }     
            //добавляем права на группу Администратор
            DBExecuteNew($db_ak, "INSERT INTO ".$id."_permis_unit (api_name, id_unit, type) VALUE ('*/*',1,1)");


            $this->result=array('result'=>'Ok', 'Id'=>$result, 'res3'=>$s);
        }

?>