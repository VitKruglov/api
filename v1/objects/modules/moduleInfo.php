<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о клиенте по ID                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam)){
    $data=$this->urlParam;
        if(isset($data['pref']))
            $pref=$data['pref'];
}

$result=array();

if (count($this->urlData)==1){
    $id=$this->urlData[0];
    if($id==0){
        $result=DBQueryNew($this->conn, "SELECT id FROM ".$pref.$this->table_name." LIMIT 1");
        $id=$result[0];
    }
    if($id>0){
    
        $query="SELECT * FROM ".$pref.$this->table_name." WHERE id=".$id;

        $result=DBQueryNew($this->conn, $query);

        if($result['error'])
            $this->result=array('error'=>$result['error'],'query'=>$query);
        elseif(count($result)>0){
            //получаем название столбцов
			$query="SHOW COLUMNS FROM ".$pref.$this->table_name;	
			$res_columns=DBFetchNew($this->conn, $query);
			$i=0;
			while (count($res_columns)>$i)
			{
    			list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
				$arr[$i]=strtolower($name_column);
                $arr_type[$i]=$type_column;
    			$i++;
			}
            
            $res=array();

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

           //--------------------------------- таблицы, привязанные к модулю----------------------------//
           $arr[$i]='addtable';
           $i++;
           $arr[$i]='newtable';
           $i++;
           $arr[$i]='tables';
           $i++;

           $a=DBFetchNew($this->conn, "SELECT TABLE_NAME,TABLE_NAME  FROM information_schema.tables WHERE TABLE_SCHEMA='crom3' $comp ORDER BY TABLE_NAME ");
           $res['data_type']['addtable']['table']=array();
           foreach($a as $j => $b) $res['data_type']['addtable']['table']=$res['data_type']['addtable']['table']+array($b[0] => $b[1]);
           $res['data_type']['addtable']['type']='select';
           
           $res_eq=DBFetchNew($this->conn,"SELECT name_table,concat('<input type=\"checkbox\" name=\"def[',name_table,']\" ',if(copy_data='y','checked',''),'>'), concat('<input type=\"checkbox\" name=\"unset[',name_table,']\">') FROM modules_tables as mt WHERE mt.module_id='$id' ORDER BY name_table");
           
           #if(count($res_eq)>0){
                $res['data_type']['newtable']['type']='button';
                $res['newtable']='Добавить таблицу';

                $res['tables']=$res_eq;
                $res['tables']=$res['tables']+array('name'=>array('таблица',"данные по <br>умолчанию","<input type=\"submit\" name=\"submit\" value=\"Отвязать\" class=\"btn btn-outline-primary\">"));
                $res['data_type']['tables']['type']='table';   
                
                 	
                //наличие и соответсвие таблиц у АК
                $res_ak=DBFetchNew($this->conn,"SELECT id, realname FROM owner WHERE id>0");
                $res_tbl=DBFetchNew($this->conn,"SELECT name_table FROM modules_tables WHERE module_id=".$id." ORDER BY name_table");

                for($i=0;$i<count($res_ak);$i++){
                    //перебираем все АК 
                    list($pref_ak,$name_ak)=$res_ak[$i];
                    $pref_ak=$pref_ak."_";

                    unset($isset_button);

                    array_push($res['tables']['name'],$pref_ak);

                    $db_ak = DBConnect($tmpl, $this->config, $pref_ak);

                #    $res['config']=$res;
                    for($j=0;$j<count($res_tbl);$j++){
                        list($name_tbl)=$res_tbl[$j];

                        $tbl_exist=DBQueryNew($db_ak,"SELECT * FROM information_schema.tables WHERE table_name = '".$pref_ak.$name_tbl."' LIMIT 1");

                        if(count($tbl_exist)==0){
                            if(!$isset_button)
                                $res['tables']['name'][$i+2]=$res['tables']['name'][$i+2]." <input type=\"submit\" name=\"submit\" value=\"".$pref_ak."Исправить\" class=\"btn btn-outline-primary btn-sm\">";
                            $isset_button=1;
                            $res['tables'][$j][count($res['tables'][$j])]='<font color=red>отсутствует таблица</font>';
                        }else{
                            
                            $arr_tbl_ak=DBFetchNew($db_ak, "SHOW COLUMNS FROM ".$pref_ak.$name_tbl);
                            $arr_tbl_main=DBFetchNew($this->conn, "SHOW COLUMNS FROM ".$name_tbl);	

                            if(array_column($arr_tbl_main, '0')===array_column($arr_tbl_ak, '0'))
                                $res['tables'][$j][count($res['tables'][$j])]='ok';
                            else{
                                if(!$isset_button)
                                    $res['tables']['name'][$i+2]=$res['tables']['name'][$i+2]." <input type=\"submit\" name=\"submit\" value=\"".$pref_ak."Исправить\" class=\"btn btn-outline-primary btn-sm\">";
                                $isset_button=1;
                                $s="<font color=red>отсутствует столбец </font>";
                                foreach($arr_tbl_main as $key=>$val){
                                    if(!$arr_tbl_ak[$key]){
                                        $s=$s."<br>".$val[0];
                                    }
                                }
                                if($s!="<font color=red>отсутствует столбец </font>")
                                    $res['tables'][$j][count($res['tables'][$j])]=$s;
                            }
                            
                        }
                    }
                    
                }
         	

        //---------------------------------------------------------------------------------------------------//   

            $arr_name=array('ID','Название','Доступ','Примечание','','','Привязанные таблицы');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;
                        
            $this->result=$res;
            
        }else{
            $this->result=array('message'=>404);
        }

    }
}




?>