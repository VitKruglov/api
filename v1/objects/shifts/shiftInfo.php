<?php
//-------------------------------------------------------------//
//                                                             //
//        информация о смене               //
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
            

            $arr_name=array('ID','Изменено','Изменил','Активный','№ заявки','Договор','Фактическое время начала','Фактическое время конца','Фактическое кол-во часов','Оплачиваемое кол-во часов','Фактическое кол-во штук','ед.изм.','Запланировано','Запланировал','Снято','Снял','Дополнительная информация', 'Планируемое время начала','Планируемое время конца','Планируемое кол-во часов','Планируемое Оплачиваемое кол-во часов','Планируемое кол-во штук','планируемая ставка оплаты в час','планируемая оплата за смену','Планируемый тип единицы измерения','планируемая ставка оплаты для единицы измерений','Специализация','Сумма за день или часы','Сумма за единицы');

            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_plan']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_plan']['table']=$res['data_type']['id_person_plan']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_plan']['readonly']="readonly";

            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION  SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_remove']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_remove']['table']=$res['data_type']['id_person_remove']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT id, id FROM ".$pref."orders_requests");
            $res['data_type']['id_request']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_request']['table']=$res['data_type']['id_request']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT ".$pref."lp_contract.id, concat (if(t.name IS NOT NULL and t.name!='',t.name,''),' ', if(m.sname IS NOT NULL and m.sname!='',m.sname,''),' ',if(m.name IS NOT NULL and m.name!='',m.name,''),' (',w.number,')') FROM ".$pref."lp_contract, ".$pref."lp_worker as w, ".$pref."lp_man as m, ".$pref."type_contract as t WHERE w.id=".$pref."lp_contract.id_worker and m.id=w.id_man and t.id=".$pref."lp_contract.type");
            $res['data_type']['id_contract']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_contract']['table']=$res['data_type']['id_contract']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."clients_type_units as d");
            $res['data_type']['id_type_unit']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_type_unit']['table']=$res['data_type']['id_type_unit']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."speciality as d");
            $res['data_type']['id_speciality']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_speciality']['table']=$res['data_type']['id_speciality']['table']+array($b[0] => $b[1]);  

            $res['data_type']['dt_plan']['readonly']="readonly";

            $res['data_type']['plan_dt_begin']['readonly']="readonly";
            $res['data_type']['plan_dt_end']['readonly']="readonly";
            $res['data_type']['plan_cnt_hour']['readonly']="readonly";
            $res['data_type']['plan_cost_hour']['readonly']="readonly";
            $res['data_type']['plan_rate_hour']['readonly']="readonly";
            $res['data_type']['plan_rate_day']['readonly']="readonly";
            $res['data_type']['plan_rate_unit']['readonly']="readonly";
            $res['data_type']['plan_type_unit']['readonly']="readonly";
            
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
            $arr_name=array('ID','Изменено','Изменил','Активный','№ заявки','Договор','Фактическое время начала','Фактическое время конца','Фактическое кол-во часов','Оплачиваемое кол-во часов','Фактическое кол-во штук','ед.изм.','Запланировано','Запланировал','Снято','Снял','Дополнительная информация', 'Планируемое время начала','Планируемое время конца','Планируемое кол-во часов','Планируемое Оплачиваемое кол-во часов','Планируемое кол-во штук','планируемая ставка оплаты в час','планируемая оплата за смену','Планируемый тип единицы измерения','планируемая ставка оплаты для единицы измерений','Специализация','Сумма за день или часы','Сумма за единицы');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT NULL as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_plan']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_plan']['table']=$res['data_type']['id_person_plan']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_plan']['readonly']="readonly";

            $a=DBFetchNew($this->conn, "SELECT 'NULL' as id, NULL as realname UNION SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_remove']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_remove']['table']=$res['data_type']['id_person_remove']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT id, id FROM ".$pref."orders_requests");
            $res['data_type']['id_request']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_request']['table']=$res['data_type']['id_request']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT ".$pref."lp_contract.id, concat (if(t.name IS NOT NULL and t.name!='',t.name,''),' ', if(m.sname IS NOT NULL and m.sname!='',m.sname,''),' ',if(m.name IS NOT NULL and m.name!='',m.name,''),' (',w.number,')') FROM ".$pref."lp_contract, ".$pref."lp_worker as w, ".$pref."lp_man as m, ".$pref."type_contract as t WHERE w.id=".$pref."lp_contract.id_worker and m.id=w.id_man and t.id=".$pref."lp_contract.type");
            $res['data_type']['id_contract']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_contract']['table']=$res['data_type']['id_contract']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."clients_type_units as d");
            $res['data_type']['id_type_unit']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_type_unit']['table']=$res['data_type']['id_type_unit']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT '','' UNION SELECT d.id, d.name FROM ".$pref."speciality as d");
            $res['data_type']['id_speciality']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_speciality']['table']=$res['data_type']['id_speciality']['table']+array($b[0] => $b[1]);  

            $res['data_type']['dt_plan']['readonly']="readonly";

            //планируемые данные
             
            $res['data_type']['plan_dt_begin']['readonly']="readonly";
            $res['data_type']['plan_dt_end']['readonly']="readonly";
            $res['data_type']['plan_cnt_hour']['readonly']="readonly";
            $res['data_type']['plan_cost_hour']['readonly']="readonly";
            $res['data_type']['plan_rate_hour']['readonly']="readonly";
            $res['data_type']['plan_rate_day']['readonly']="readonly";
            $res['data_type']['plan_rate_unit']['readonly']="readonly";
            $res['data_type']['plan_type_unit']['readonly']="readonly";
            
            $res['data_type']['plan_sum']['type']='text';
            $res['data_type']['plan_sum']['name']='Планируемое вознаграждение';

            $this->result=$res;
            
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>