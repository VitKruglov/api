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
    list($name_field,$name_key)=explode('as ',$val);
    $name_field=trim($name_field);
    $name_key=trim($name_key);
    $name_key=str_replace("'","",$name_key);
    if(isset($data['search'][$name_key])){
        $data['search'][$name_field]=$data['search'][$name_key];
        unset($data['search'][$name_key]);
    }
    if(isset($data['like'][$name_key])){
        $data['like'][$name_field]=$data['like'][$name_key];
        unset($data['like'][$name_key]);
    }
    $arr2_key[$k]=$name_key;
}

if(isset($data['search'])){
    foreach($data['search'] as $name_search=>$val2)
        if(gettype($val2)=='string')
            $where = $where." AND ".$name_search."='".$val2."'";
        else
            $where = $where." AND ".$name_search."=".$val2;
}
if(isset($data['like'])){
    foreach($data['like'] as $name_search=>$val2)
        $where = $where." AND ".$name_search." like '%".$val2."%'";
}
$query.=" WHERE 1=1 $where ORDER BY $sort";

//------------------------------выполняем запрос--------------------------------//
$result=DBFetchNew($this->conn, $query);
//------------------------------------------------------------------------------//

if($result['error'])
    $this->result=array('error'=>$result['error'],'query'=>$query);
elseif(count($result)>0){
    $res=array();
    foreach($result as $key=>$val){
        foreach($val as $key2=>$val2){   
            $res[$key][$arr2_key[$key2]]=$val2;  
            if($arr2_key[$key2]=='state' and is_null($val2))
                $res[$key][$arr2_key[$key2]]=1;
        }
    }
/*            
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
*/
    $this->result=$res+$arr_sort+array('colspan'=>count($result[0])+1)+array('data'=>$res2)+array('get'=>$this->getParam)+array('query'=>$query);
    
}else{
    $this->result=$arr_sort;
}
?>
