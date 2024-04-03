<?php
//-------------------------------------------------------------//
//                                                             //
//                 одинаковая часть для всех                   //
//                   object файлов All - формирование result                             //
//-------------------------------------------------------------//

//разбираем $query и выдергиваем названия полей 
list($names)=explode('FROM', $query);
list($x,$names)=explode('SELECT ',$names);
$arr_key=explode(',',$names);
foreach ($arr_key as $k=>$val){
    list($x,$name_key)=explode('as ',mb_strtolower($val));
    $name_key=trim($name_key);
    $arr2_key[$k]=$name_key;
}

if($result['error'])
    $this->result=array('error'=>$result['error'],'query'=>$query);
elseif(count($result)>0){
    $res=array();
    foreach($result as $key=>$val){
        foreach($val as $key2=>$val2)    
            $res[$key][$arr2_key[$key2]]=$val2;  
        $res[$key]['url_info']="<a href=\"".$name_script_info."?id=".$res[$key]['id']."\">Инфо.</a>";
    }
            
    //получаем название столбцов
    $res_columns=DBFetchNew($this->conn, "SHOW COLUMNS FROM ".$pref.$this->table_name);
    $i=0;
    while (count($res_columns)>$i)
    {
        list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
        $arr[$i]=$name_column;
        $i++;
    } 
    //формируем чистые данные
    $res2=array();
    $result_2=DBFetchNew($this->conn, "SELECT * FROM ".$pref.$this->table_name." as tbl1 WHERE 1=1 $where ORDER BY $sort");
    foreach($result_2 as $key=>$val)
        foreach($val as $key2=>$val2)    
            $res2[$key][$arr[$key2]]=$val2;  

    $this->result=$res+$arr_sort+array('colspan'=>count($result[0])+1)+array('data'=>$res2)+array('get'=>$this->getParam);
    
}else{
    $this->result=$arr_sort+array('colspan'=>count($arr_sort['sort']));
}
?>
