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
            //получаем название столбцов
			$query="SHOW COLUMNS FROM ".$pref.$this->table_name;	
			$res_columns=DBFetchNew($this->conn, $query);
			$i=0;
			while (count($res_columns)>$i)
			{
    			list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
				$arr[$i]=strtolower($name_column);
                $res[strtolower($name_column)]='';
                $res['data_type'][strtolower($name_column)]['type']=$arr_type[$key];
    			$i++;
			}

            $arr_name=array('ID','Дата','Изменено','Изменил','Заверено','Заверил','Активный','Тип операции','Договор','Операция привязана к','таблица','Ставка','Сумма общая','Примечание','Сумма учтенная','Цена за единицу','Цена за день','Цена за час','Кол-во часов','Кол-во единиц','Тип единицы','Сумма за единицы');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            $res['data_type']['dt']['readonly']='readonly';
            $res['data_type']['dt_signature']['readonly']='readonly';
            $res['data_type']['sum_paid']['readonly']='readonly';            
            $res['data_type']['name_operation']['readonly']='readonly';        
            
            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_signature']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_signature']['table']=$res['data_type']['id_person_signature']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_signature']['readonly']='readonly';
  
            $a=DBFetchNew($this->conn, "SELECT 0, NULL UNION SELECT id, name FROM ".$pref."pay_type");
            $res['data_type']['id_type']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_type']['table']=$res['data_type']['id_type']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT null,null UNION SELECT ".$pref."lp_contract.id, concat (if(t.name IS NOT NULL and t.name!='',t.name,''),' ', if(m.sname IS NOT NULL and m.sname!='',m.sname,''),' ',if(m.name IS NOT NULL and m.name!='',m.name,''),' (',w.number,')') FROM ".$pref."lp_contract, ".$pref."lp_worker as w, ".$pref."lp_man as m, ".$pref."type_contract as t WHERE w.id=".$pref."lp_contract.id_worker and m.id=w.id_man and t.id=".$pref."lp_contract.type");
            $res['data_type']['id_contract']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_contract']['table']=$res['data_type']['id_contract']['table']+array($b[0] => $b[1]);    
            
            $a=DBFetchNew($this->conn, "SELECT 0 as id,null as name UNION SELECT ".$pref."clients_rate.id , concat(".$pref."speciality.name,' / ',".$pref."clients_department.name) 
            FROM ".$pref."clients_rate, ".$pref."speciality, ".$pref."clients_department WHERE ".$pref."speciality.id=".$pref."clients_rate.id_speciality and ".$pref."clients_department.id=".$pref."clients_rate.id_department");
            $res['data_type']['id_rate']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_rate']['table']=$res['data_type']['id_rate']['table']+array($b[0] => $b[1]); 

            $res['data_type']['id_operation']['table']=array();

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."clients_type_units as d");
            $res['data_type']['id_type_unit']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_type_unit']['table']=$res['data_type']['id_type_unit']['table']+array($b[0] => $b[1]);  

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

            list($signature)=DBQueryNew($this->conn, "SELECT 1 FROM ".$pref."pay_balance WHERE id='$id' and id_person_signature>0 and dt_signature is not NULL");
            if(!$signature){
                $res['data_type']['signature']['type']='button';
                $res['data_type']['signature']['name']='';
                $res['signature']='Заверено';
            }else{
                $res['data_type']['signature']['type']='button';
                $res['data_type']['signature']['name']='';
                $res['signature']='Разверено';
            }

            list($name_operation)=DBQueryNew($this->conn, "SELECT b.name_operation FROM ".$pref."pay_balance as b WHERE b.id=$id");

            if($name_operation!=NULL){
                $res['name_operation']=$name_operation;
                if($name_operation=='orders_shifts')
                    $query_operation="SELECT null, '' UNION SELECT s.id, concat('Смена №',s.id,' ',if(s.dt_begin IS NOT NULL, s.dt_begin,''),' - ',if(s.dt_end IS NOT NULL, s.dt_end,'')) FROM ".$pref."orders_shifts as s WHERE s.id_contract=".$res['id_contract'];
                elseif($name_operation=="pay_pay")
                    $query_operation="SELECT s.id, s.note FROM ".$pref."pay_pay as s WHERE s.id=".$res['id_operation'];

                $a=DBFetchNew($this->conn, $query_operation);
                $res['data_type']['id_operation']['table']=array();
                $res['data_type']['id_operation']['query_operation']=$query_operation;
                foreach($a as $j => $b) $res['data_type']['id_operation']['table']=$res['data_type']['id_operation']['table']+array($b[0] => $b[1]);
            }else{
                $res['data_type']['id_operation']['table']=array();
            }

            $arr_name=array('ID','Дата','Изменено','Изменил','Когда заверено','Кто заверил','Активный','Тип операции','Договор','Операция привязана к','таблица','Ставка','Сумма общая','Примечание','Сумма учтенная','Цена за единицу','Цена за день','Цена за час','Кол-во часов','Кол-во единиц','Тип единицы','Сумма за единицы');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            $res['data_type']['dt']['readonly']='readonly';
            $res['data_type']['dt_signature']['readonly']='readonly';
            $res['data_type']['sum_paid']['readonly']='readonly';            
            $res['data_type']['name_operation']['readonly']='readonly';      
            
            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_signature']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_signature']['table']=$res['data_type']['id_person_signature']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_signature']['readonly']='readonly';
  
            $a=DBFetchNew($this->conn, "SELECT 0, NULL UNION SELECT id, name FROM ".$pref."pay_type");
            $res['data_type']['id_type']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_type']['table']=$res['data_type']['id_type']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT null,null UNION SELECT ".$pref."lp_contract.id, concat (if(t.name IS NOT NULL and t.name!='',t.name,''),' ', if(m.sname IS NOT NULL and m.sname!='',m.sname,''),' ',if(m.name IS NOT NULL and m.name!='',m.name,''),' (',w.number,')') FROM ".$pref."lp_contract, ".$pref."lp_worker as w, ".$pref."lp_man as m, ".$pref."type_contract as t WHERE w.id=".$pref."lp_contract.id_worker and m.id=w.id_man and t.id=".$pref."lp_contract.type");
            $res['data_type']['id_contract']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_contract']['table']=$res['data_type']['id_contract']['table']+array($b[0] => $b[1]);    
            
            $a=DBFetchNew($this->conn, "SELECT 0 as id,null as name UNION SELECT ".$pref."clients_rate.id , concat(".$pref."speciality.name,' / ',".$pref."clients_department.name) 
            FROM ".$pref."clients_rate, ".$pref."speciality, ".$pref."clients_department WHERE ".$pref."speciality.id=".$pref."clients_rate.id_speciality and ".$pref."clients_department.id=".$pref."clients_rate.id_department");
            $res['data_type']['id_rate']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_rate']['table']=$res['data_type']['id_rate']['table']+array($b[0] => $b[1]); 

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."clients_type_units as d");
            $res['data_type']['id_type_unit']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_type_unit']['table']=$res['data_type']['id_type_unit']['table']+array($b[0] => $b[1]);  

            $this->result=$res;

        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>