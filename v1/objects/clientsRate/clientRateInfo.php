<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о ставке                          //
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
            $arr_name=array('ID','Специальность','Отдел','Ставка за час','Часов в смене','Ставка за смену','Ставка за единицу','Единица измерения','Базовые требования','Расширенные требования','Дополнительная информация','Активный','Дата изменения','Кто изменял');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_change']['readonly']="readonly";
            
            $a=DBFetchNew($this->conn, "SELECT id, name FROM ".$pref."speciality");
            $res['data_type']['id_speciality']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_speciality']['table']=$res['data_type']['id_speciality']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT 0,'Все' UNION SELECT d.id, concat(c.name,' ',o.name,' ',d.name) FROM ".$pref."clients_department as d LEFT JOIN ".$pref."clients_object as o ON o.id=d.id_object LEFT JOIN ".$pref."clients_group as g ON g.id=o.id_group LEFT JOIN ".$pref."clients as c ON c.id=g.id_client");
            $res['data_type']['id_department']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_department']['table']=$res['data_type']['id_department']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."clients_type_units as d");
            $res['data_type']['id_type_unit']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_type_unit']['table']=$res['data_type']['id_type_unit']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT null,' ' UNION SELECT id, concat(if(gender='male','муж., ',if(gender='female','жен., ',if(gender='all','все, ',''))),' Возраст ',if(age_after IS NOT NULL and age_after!='', age_after,''),'-',if(age_before IS NOT NULL and age_before!='', age_before,''),', ',if(national='yes',' гражданство РФ Да, ',if(national='no',' гражданство РФ Нет, ','')),if(passport='yes',' паспорт Да, ',if(passport='no',' паспорт Нет, ','')),if(med='yes',' мед.книжка Да',if(med='no',' мед.книжка Нет',''))) FROM ".$pref."clients_requirements");
            $res['data_type']['id_req']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_req']['table']=$res['data_type']['id_req']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."set_requirements as d");
            $res['data_type']['id_set']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_set']['table']=$res['data_type']['id_set']['table']+array($b[0] => $b[1]);

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
            $arr_name=array('ID','Специальность','Отдел','Ставка за час','Часов в смене','Ставка за смену','Ставка за единицу','Единица измерения','Базовые требования','Расширенные требования','Дополнительная информация','Активный','Дата изменения','Кто изменял');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_change']['readonly']="readonly";

            $a=DBFetchNew($this->conn, "SELECT id, name FROM ".$pref."speciality");
            $res['data_type']['id_speciality']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_speciality']['table']=$res['data_type']['id_speciality']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT 0,'Все' UNION SELECT d.id, concat(c.name,' ',o.name,' ',d.name) FROM ".$pref."clients_department as d LEFT JOIN ".$pref."clients_object as o ON o.id=d.id_object LEFT JOIN ".$pref."clients_group as g ON g.id=o.id_group LEFT JOIN ".$pref."clients as c ON c.id=g.id_client");
            $res['data_type']['id_department']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_department']['table']=$res['data_type']['id_department']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."clients_type_units as d");
            $res['data_type']['id_type_unit']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_type_unit']['table']=$res['data_type']['id_type_unit']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT null,' ' UNION SELECT id, concat(if(gender='male','муж., ',if(gender='female','жен., ',if(gender='all','все, ',''))),' Возраст ',if(age_after IS NOT NULL and age_after!='', age_after,''),'-',if(age_before IS NOT NULL and age_before!='', age_before,''),', ',if(national='yes',' гражданство РФ Да, ',if(national='no',' гражданство РФ Нет, ','')),if(passport='yes',' паспорт Да, ',if(passport='no',' паспорт Нет, ','')),if(med='yes',' мед.книжка Да',if(med='no',' мед.книжка Нет',''))) FROM ".$pref."clients_requirements");
            $res['data_type']['id_req']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_req']['table']=$res['data_type']['id_req']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."set_requirements as d");
            $res['data_type']['id_set']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_set']['table']=$res['data_type']['id_set']['table']+array($b[0] => $b[1]);

            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>