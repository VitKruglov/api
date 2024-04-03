<?php
//-------------------------------------------------------------//
//                                                             //
//                 одинаковая часть для всех                   //
//           object Add - проверка вводимых данных             //
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
        $arr_change[$new_name]=$name_column;
    }

    $i++;
}       
unset ($this->postParam['input']['id']);

foreach($this->postParam['input'] as $name_column=>$val){
    //проверка на телефон
    if(strpos(mb_strtolower($name_column), 'phone')!==false){
        $val=change_phone($val);
    }
    if(isset($arr[$name_column]) ){
        if(substr($arr[$name_column],0,7)=='varchar' or $arr[$name_column]=='text')
            $val=addslashes($val);

        if(((substr($arr[$name_column],0,9)=='timestamp' or substr($arr[$name_column],0,4)=='date' or substr($arr[$name_column],0,8)=='smallint' or substr($arr[$name_column],0,3)=='int' or substr($arr[$name_column],0,4)=='enum') or $arr[$name_column]=='float') and $val==''){
            $query.=", `".$name_column."`";
            $query2.=", NULL";
        }elseif((substr($arr[$name_column],0,9)=='timestamp' or substr($arr[$name_column],0,8)=='datetime') and $val=='NOW()'){
            $query.=", `".$name_column."`";
            $query2.=", NOW()";
        }elseif(substr($arr[$name_column],0,8)=='smallint' or substr($arr[$name_column],0,3)=='int' or substr($arr[$name_column],0,6)=='bigint' or $arr[$name_column]=='float'){
            $query.=", `".$name_column."`";
            $query2.=", ".$val;
        }else{
            $query.=", `".$name_column."`";
            $query2.=", '".$val."'";
        }
    }elseif(isset($arr_change[$name_column]) and isset($arr[$arr_change[$name_column]])){
        if(substr($arr[$arr_change[$name_column]],0,7)=='varchar' or $arr[$arr_change[$name_column]]=='text')
            $val=addslashes($val);

        if((substr($arr[$arr_change[$name_column]],0,9)=='timestamp' or substr($arr[$arr_change[$name_column]],0,4)=='date' or substr($arr[$arr_change[$name_column]],0,8)=='smallint' or substr($arr[$arr_change[$name_column]],0,3)=='int' or substr($arr[$arr_change[$name_column]],0,4)=='enum' or $arr[$arr_change[$name_column]]=='float') and $val==''){
            $query.=", `".$arr_change[$name_column]."`";
            $query2.=", NULL";
        }elseif((substr($arr[$arr_change[$name_column]],0,9)=='timestamp' or substr($arr[$arr_change[$name_column]],0,8)=='datetime') and $val=='NOW()'){
            $query.=", `".$arr_change[$name_column]."`";
            $query2.=", NOW()";
        }elseif(substr($arr[$arr_change[$name_column]],0,8)=='smallint' or substr($arr[$arr_change[$name_column]],0,3)=='int' or substr($arr[$arr_change[$name_column]],0,6)=='bigint' or $arr[$arr_change[$name_column]]=='float'){
            $query.=", `".$arr_change[$name_column]."`";
            $query2.=", ".$val;
        }else{
            $query.=", `".$arr_change[$name_column]."`";
            $query2.=", '".$val."'";
        }
    }
}
$query= substr($query,2);
$query2= substr($query2,2);

$query="INSERT INTO ".$pref.$this->table_name." (".$query.") VALUE (".$query2.")";
?>
