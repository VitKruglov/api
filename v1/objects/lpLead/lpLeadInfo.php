<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о лиде                //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(!is_null($data['data_from_include_api']) and is_array($data['data_from_include_api']))
        $arr_fias=$data['data_from_include_api'];
}

$result=array();

if (count($this->urlData)==1){
    list($nbd)=explode("_",$pref);
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
            $arr_name=array('ID','Активный','Дата изменения','Кто изменял','Имя','Адрес','Адрес ФИАС','Электронная почта','Телефон','Источник','Состояние','Дополнительная информация');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_change']['readonly']="readonly";

            $a=DBFetchNew($this->conn, "SELECT '', ' ' as name UNION SELECT id,address FROM ".$pref."fias WHERE level=8 ORDER BY name");
            $res['data_type']['id_fiass']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_fiass']['table']=$res['data_type']['id_fiass']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."type_condition as d");
            $res['data_type']['id_condition']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_condition']['table']=$res['data_type']['id_condition']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."type_source as d");
            $res['data_type']['id_source']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_source']['table']=$res['data_type']['id_source']['table']+array($b[0] => $b[1]); 
            
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
			for($i=0;$i<count($res_columns);$i++)
			{
    			list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
				$arr[$i]=strtolower($name_column);
                $arr_type[$i]=$type_column;
			}

            $arr[$i]='fiassearch';
            $i++;
            $arr[$i]='newfias';
            $i++;
            
            $res=array();

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

            $res['name1']=$data['data_from_include_api'];

            //строка поиска адреса по справочнику ФИАС
            if(isset($arr_fias)){
                $res['data_type']['fiassearch']['type']='select';  
                $res['data_type']['fiassearch']['table']=$arr_fias;
                $res['data_type']['newfias']['type']='button';
                $res['newfias']='Добавить адрес ФИАС';
            }else{
                $res['data_type']['fiassearch']['type']='varchar';  
                $res['data_type']['fiassearch']['placeholder']=' placeholder="введите адрес вручную"';
                $res['fiassearch']='';
                $res['data_type']['newfias']['type']='button';
                $res['newfias']='Найти адрес ФИАС';
            }
         
            $arr_name=array('ID','Активный','Дата изменения','Кто изменял','Имя','Адрес','Адрес ФИАС','Электронная почта','Телефон','Источник','Состояние','Дополнительная информация','Поиск адреса','');
            foreach($arr_name as $i=>$name_td)
                $res['data_type'][$arr[$i]]['name']=$name_td;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_change']['readonly']="readonly";

            $a=DBFetchNew($this->conn, "SELECT '', ' ' as name UNION SELECT id,address FROM ".$pref."fias WHERE level=8 ORDER BY name");
            $res['data_type']['id_fiass']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_fiass']['table']=$res['data_type']['id_fiass']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."type_condition as d");
            $res['data_type']['id_condition']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_condition']['table']=$res['data_type']['id_condition']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."type_source as d");
            $res['data_type']['id_source']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_source']['table']=$res['data_type']['id_source']['table']+array($b[0] => $b[1]); 
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}
?>