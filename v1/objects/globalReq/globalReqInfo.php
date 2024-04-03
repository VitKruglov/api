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
            $arr_name=array('ID','Клиентозависимое','Название','Тип','Скрытое','Строгость по умолчанию','Примечание');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT 'y', 'Да' UNION SELECT 'n', 'Нет'");
            $res['data_type']['clients']['table']=array();
            foreach($a as $j => $b) $res['data_type']['clients']['table']=$res['data_type']['clients']['table']+array($b[0] => $b[1]); 
            $res['data_type']['clients']['type']='checkbox';
            $res['data_type']['clients']['value']='n';

            $a=DBFetchNew($this->conn, "SELECT 'y', 'Да' UNION SELECT 'n', 'Нет'");
            $res['data_type']['hidden']['table']=array();
            foreach($a as $j => $b) $res['data_type']['hidden']['table']=$res['data_type']['hidden']['table']+array($b[0] => $b[1]); 
            $res['data_type']['hidden']['type']='checkbox';
            $res['data_type']['hidden']['value']='n';

            $a=DBFetchNew($this->conn, "SELECT 'y', 'Да' UNION SELECT 'n', 'Нет'");
            $res['data_type']['required']['table']=array();
            foreach($a as $j => $b) $res['data_type']['required']['table']=$res['data_type']['required']['table']+array($b[0] => $b[1]); 
            $res['data_type']['required']['type']='checkbox';
            $res['data_type']['required']['value']='n';

            $a=DBFetchNew($this->conn, "SELECT '1','Одежда' UNION SELECT '2','Навык' UNION SELECT '3','Документ'");
            $res['data_type']['type']['table']=array();
            $res['data_type']['type']['type']='select';
            foreach($a as $j => $b) $res['data_type']['type']['table']=$res['data_type']['type']['table']+array($b[0] => $b[1]);  
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>