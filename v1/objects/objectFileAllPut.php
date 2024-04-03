<?php
//-------------------------------------------------------------//
//                                                             //
//                 одинаковая часть для всех                   //
//           object Put - проверка вводимых данных             //
//-------------------------------------------------------------//
        //получаем название столбцов
		$res_columns=DBFetchNew($this->conn, "SHOW COLUMNS FROM ".$pref.$this->table_name);
		$i=0;
		while (count($res_columns)>$i)
		{
    		list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
			$arr[$name_column]=$type_column;

            unset($new_name);
            $a=explode('_',$name_column);
            if(count($a)>1){
                $new_name=$a[0];
                for($j=1;$j<count($a);$j++)
                    $new_name.=mb_strtoupper(substr($a[$j],0,1)).substr($a[$j],1);
                $arr_change[$new_name]=$name_column;                                    //  $arr_change[idTypeUnit]=id_type_unit
                $arr_change2[$name_column]=$new_name;                                   //  $arr_change2[id_type_unit]=idTypeUnit
            }
    		$i++;
		}       
        
        foreach($this->postParam['input'] as $name_column=>$val){
            //проверка на телефон
            if(strpos(mb_strtolower($name_column), 'phone')!==false){
                $val=change_phone($val);
            }
            
            if(mb_strtolower($val)=='null' and isset($arr[$name_column]))
                $query.=", ".$pref.$this->table_name.".".$name_column."=NULL";
            elseif(mb_strtolower($val)=='null' and isset($arr_change[$name_column]))
                $query.=", ".$pref.$this->table_name.".".$arr_change[$name_column]."=NULL";
            elseif(isset($arr[$name_column]) ){
                if(substr($arr[$name_column],0,7)=='varchar' or $arr[$name_column]=='text' or substr($arr[$name_column],0,8)=='tinytext')
                    $val=addslashes($val);
                if((substr($arr[$name_column],0,9)=='timestamp' or substr($arr[$name_column],0,4)=='date' or substr($arr[$name_column],0,8)=='smallint' or substr($arr[$name_column],0,3)=='int' or substr($arr[$name_column],0,4)=='enum' or $arr[$name_column]=='float') and $val==''){
                    $query.=", ".$pref.$this->table_name.".".$name_column."=NULL";
                }elseif((substr($arr[$name_column],0,9)=='timestamp' or substr($arr[$name_column],0,4)=='date') and $val=='NOW()'){
                    $query.=", ".$pref.$this->table_name.".".$name_column."=NOW()";
                }elseif(substr($arr[$name_column],0,8)=='smallint' or substr($arr[$name_column],0,3)=='int' or substr($arr[$name_column],0,6)=='bigint' or $arr[$name_column]=='float'){
                    $query.=", ".$pref.$this->table_name.".".$name_column."=".$val;
                }else
                    $query.=", ".$pref.$this->table_name.".".$name_column."='".$val."'";
            }elseif(isset($arr_change[$name_column]) and isset($arr[$arr_change[$name_column]])){
                if(substr($arr[$arr_change[$name_column]],0,7)=='varchar' or $arr[$arr_change[$name_column]]=='text' or substr($arr[$arr_change[$name_column]],0,8)=='tinytext')
                    $val=addslashes($val);
                if((substr($arr[$arr_change[$name_column]],0,9)=='timestamp' or substr($arr[$arr_change[$name_column]],0,4)=='date' or substr($arr[$arr_change[$name_column]],0,8)=='smallint' or substr($arr[$arr_change[$name_column]],0,3)=='int' or substr($arr[$arr_change[$name_column]],0,4)=='enum' or $arr[$arr_change[$name_column]]=='float') and $val=='')
                    $query.=", ".$pref.$this->table_name.".".$arr_change[$name_column]."=NULL";
                elseif((substr($arr[$arr_change[$name_column]],0,9)=='timestamp' or substr($arr[$arr_change[$name_column]],0,4)=='date') and $val=='NOW()')
                    $query.=", ".$pref.$this->table_name.".".$arr_change[$name_column]."=NOW()";
                elseif(substr($arr[$arr_change[$name_column]],0,8)=='smallint' or substr($arr[$arr_change[$name_column]],0,3)=='int' or substr($arr[$arr_change[$name_column]],0,6)=='bigint' or $arr[$arr_change[$name_column]]=='float')
                    $query.=", ".$pref.$this->table_name.".".$arr_change[$name_column]."=".$val;
                else
                    $query.=", ".$pref.$this->table_name.".".$arr_change[$name_column]."='".$val."'";
            }
        }

        $query= substr($query,2);
        $query="UPDATE ".$pref.$this->table_name." SET ".$query;
        if($id>0)
            $query.=" WHERE id=".$id;
        elseif(isset($query_where))
            $query.=$query_where;
?>
