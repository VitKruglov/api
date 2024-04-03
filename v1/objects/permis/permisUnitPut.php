<?php
//-------------------------------------------------------------//
//                                                             //
//        изменение прав для ролей                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->postParam['pref'])){
    $pref=$this->postParam['pref'];
}

$result=array();

if(isset($this->postParam['input'])){
    
    //удаляем все данные из таблицы
    DBExecuteNew($this->conn, "TRUNCATE ".$pref.$this->table_name);

    foreach($this->postParam['input'] as $name_api=>$val){
        foreach($val as $id_unit=>$type){
            if(strpos($name_api,"/")===false) // для всех методов  
                $name_api=$name_api."/*";
            $query="INSERT INTO ".$pref.$this->table_name." (api_name, id_unit, type) VALUE ('$name_api', '$id_unit', $type)";
            
            if($type>0){
                $result=DBExecuteNew($this->conn, $query);
                $cnt++;
            }
        }
    }


    if($result['error']){
        $this->result=array('error'=>$result['error'],'query'=>$query);
    }else{
        $this->result=array('result'=>'Ok', 'cnt'=>'Всего записей '.$cnt); //, 'arr'=>$arr
    }

}
?>