<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о требоаниях к лп в заявке по ID                 //
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
                $res[strtolower($name_column)]='';
                $res['data_type'][strtolower($name_column)]['type']=$type_column;
    			$i++;
			}
            

            $arr_name=array('ID','Изменено','Изменил','Активный','Заявка','Базовые требования из ставок','Пол','Возраст от','Возраст до','Гражданство РФ','Наличие паспорта','Наличие медицинской книжки','Дополнительная информация','Набор расширенных требований');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;
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
            $arr_name=array('ID','Изменено','Изменил','Активный','Заявка','Базовые требования из ставок','Пол','Возраст от','Возраст до','Гражданство РФ','Наличие паспорта','Наличие медицинской книжки','Дополнительная информация','Набор расширенных требований');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;


        }else{
            $this->result=array('message'=>404);
        }
    }
                //данные из перекресных таблиц
                $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
                $res['data_type']['id_person_change']['table']=array();
                foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
      
                $a=DBFetchNew($this->conn, "SELECT r.id, concat('Заявка №',r.id,' заказа №',r.id_order,' ',o.name) FROM ".$pref."orders_requests as r, ".$pref."clients_object as o WHERE o.id=r.id_object");
                $res['data_type']['id_request']['table']=array();
                foreach($a as $j => $b) $res['data_type']['id_request']['table']=$res['data_type']['id_request']['table']+array($b[0] => $b[1]);
    
                $a=DBFetchNew($this->conn, "SELECT null,null UNION SELECT id, concat(if(gender='male','муж., ',if(gender='female','жен., ',if(gender='all','все, ',''))),' Возраст ',age_after,'-',age_before,', ',if(national='yes',' гражданство РФ Да, ',if(national='no',' гражданство РФ Нет, ',if(national='yes',' гражданство РФ Все равно, ',''))),if(passport='yes',' паспорт Да, ',if(passport='no',' паспорт Нет, ',if(passport='all',' паспорт Все равно, ',''))),if(med='yes',' мед.книжка Да',if(med='no',' мед.книжка Нет',if(med='all',' мед.книжка Все равно','')))) FROM ".$pref."clients_requirements");
                $res['data_type']['id_req']['table']=array();
                foreach($a as $j => $b) $res['data_type']['id_req']['table']=$res['data_type']['id_req']['table']+array($b[0] => $b[1]);
    
                $a=DBFetchNew($this->conn, "SELECT null,null UNION SELECT 'all', 'Все' UNION SELECT 'male', 'Мужской' UNION SELECT 'female', 'Женский'");
                $res['data_type']['gender']['table']=array();
                $res['data_type']['gender']['type']='select';
                foreach($a as $j => $b) $res['data_type']['gender']['table']=$res['data_type']['gender']['table']+array($b[0] => $b[1]);   
    
                $a=DBFetchNew($this->conn, "SELECT null,null UNION SELECT 'all', 'Все равно' UNION SELECT 'yes', 'Да' UNION SELECT 'no', 'Нет'");
                $res['data_type']['national']['table']=array();
                $res['data_type']['national']['type']='select';
                foreach($a as $j => $b) $res['data_type']['national']['table']=$res['data_type']['national']['table']+array($b[0] => $b[1]); 
                $res['data_type']['national']['value']='yes';
    
                $a=DBFetchNew($this->conn, "SELECT null,null UNION SELECT 'all', 'Все равно' UNION SELECT 'yes', 'Да' UNION SELECT 'no', 'Нет'");
                $res['data_type']['passport']['table']=array();
                $res['data_type']['passport']['type']='select';
                foreach($a as $j => $b) $res['data_type']['passport']['table']=$res['data_type']['passport']['table']+array($b[0] => $b[1]); 
                $res['data_type']['passport']['value']='yes';
    
                $a=DBFetchNew($this->conn, "SELECT null,null UNION SELECT 'all', 'Все равно' UNION SELECT 'yes', 'Да' UNION SELECT 'no', 'Нет'");
                $res['data_type']['med']['table']=array();
                $res['data_type']['med']['type']='select';
                foreach($a as $j => $b) $res['data_type']['med']['table']=$res['data_type']['med']['table']+array($b[0] => $b[1]); 
    
                $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as name UNION SELECT s.id, s.name FROM ".$pref."set_requirements as s");
                $res['data_type']['id_set']['table']=array();
                foreach($a as $j => $b) $res['data_type']['id_set']['table']=$res['data_type']['id_set']['table']+array($b[0] => $b[1]);
                
                $this->result=$res;
}




?>