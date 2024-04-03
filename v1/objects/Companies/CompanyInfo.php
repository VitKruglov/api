<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о счете клиента по ID                 //
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
            //получаем название столбцов
			$query="SHOW COLUMNS FROM ".$pref.$this->table_name;	
			$res_columns=DBFetchNew($this->conn, $query);
			$i=0;
			while (count($res_columns)>$i)
			{
    			list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
				$arr[$i]=strtolower($name_column);

                $res[strtolower($name_column)]='';  //html_entity_decode(
                $res['data_type'][strtolower($name_column)]['type']=$type_column;

    			$i++;
			}
            $arr_name=array('ID','Изменено','Изменил','Активность','Тип','Название','Название по ЕГРЮЛ','ИНН','КПП','ОГРН','Дополнительная информация');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT 'y', 'Физ.лицо' UNION SELECT 'n', 'Юр.лицо'");
            $res['data_type']['type']['table']=array();
            foreach($a as $j => $b) $res['data_type']['type']['table']=$res['data_type']['type']['table']+array($b[0] => $b[1]);            
       
            $this->result=$res;
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
            $arr_name=array('ID','Изменено','Изменил','Активность','Тип','Название','Название по ЕГРЮЛ','ИНН','КПП','ОГРН','Дополнительная информация');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT 'y', 'Физ.лицо' UNION SELECT 'n', 'Юр.лицо'");
            $res['data_type']['type']['table']=array();
            foreach($a as $j => $b) $res['data_type']['type']['table']=$res['data_type']['type']['table']+array($b[0] => $b[1]);            
       
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>