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

if(isset($this->urlData[0])){
    $id=$this->urlData[0];
    if($id>0){
        $pref_ak=$this->postParam['pref_ak'];
        $this->result=array('id'=>$id);

        $res_tbl=DBFetchNew($this->conn,"SELECT name_table, copy_data FROM modules_tables WHERE module_id=".$id." ORDER BY name_table");
        $db_ak = DBConnect($tmpl, $this->config, $pref_ak);

        for($j=0;$j<count($res_tbl);$j++){
            list($name_tbl, $copy_data)=$res_tbl[$j];

            $tbl_exist=DBQueryNew($db_ak,"SELECT * FROM information_schema.tables WHERE table_name = '".$pref_ak.$name_tbl."' LIMIT 1");

            if(count($tbl_exist)==0){
                //копируем таблицу
                $arr_tbl_main=DBQueryNew($this->conn, "SHOW CREATE TABLE ".$name_tbl);	
                DBExecuteNew($db_ak,str_replace("`".$name_tbl."`", "`".$pref_ak.$name_tbl."`", $arr_tbl_main[1]));
                $res.="Создана таблица ".$pref_ak.$name_tbl."<br>";     

                //копируем строки для unit_name
                if($copy_data=='y'){
                    $result3=DBFetchNew($this->conn,"SELECT * FROM $name_tbl ORDER BY id");
                    foreach($result3 as $i => $val){
                        DBExecuteNew($db_ak, "INSERT INTO ".$pref_ak.$name_tbl." VALUES ('".implode("','",$val)."')");
                    }   
                }       
                //копируем строки для некоторых таблиц
                if($name_tbl=='unit_name'){
                    $result3=DBFetchNew($this->conn,"SELECT * FROM unit_name_like ORDER BY id");
                    foreach($result3 as $i => $val){
                        DBExecuteNew($db_ak, "INSERT INTO ".$pref_ak.$name_tbl." VALUES ('".implode("','",$val)."')");
                    }   
                }       
            }else{
                
                $arr_tbl_ak=DBFetchNew($db_ak, "SHOW COLUMNS FROM ".$pref_ak.$name_tbl);
                $arr_tbl_main=DBFetchNew($this->conn, "SHOW COLUMNS FROM ".$name_tbl);	

                if($arr_tbl_ak===$arr_tbl_main){
                    
                }else{
                    $res='';
                    foreach($arr_tbl_main as $key=>$val){
                        if(!$arr_tbl_ak[$key]){
                            DBExecuteNew($db_ak, "ALTER TABLE ".$pref_ak.$name_tbl." ADD COLUMN ".$val[0]." ".$val[1]." AFTER ".$column_after);
                            $res.='Добавлен столбец '.$val[0]." ".$val[1]." после ".$column_after." в таблице ".$pref_ak.$name_tbl."<br>";
                        }
                        $column_after=$val[0];
                    }
                }
                
            }
        }
 
        $this->result=array('result'=>$res);
    }else{
        $this->result=array('message'=>404);
    }

 #   $this->result=array('message'=>array('id'=>$id,'ak'=>$arr_tbl_ak,'main'=>$arr_tbl_main));
    
}
?>