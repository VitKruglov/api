<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о договоре организации по ID                 //
//                                                             //
//-------------------------------------------------------------//
if(isset($this->urlParam)){
    $data=$this->urlParam;
    if(isset($data['pref']))
        $pref=$data['pref'];
    if(!is_null($data['data_from_include_api']))
        if(!isset($data['data_from_include_api']['message']))
            $arr_fias=$data['data_from_include_api'];
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

            $arr_name=array('ID','Когда изменил','Кто изменил','Активность','Организация','Контрагент','Название','Номер','Дата начала действия','Дата окончания действия','Дата подписания','НДС %','НДС тип расчета','Дополнительная информация','Счет контрагента');

            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);


            $a=DBFetchNew($this->conn, "SELECT 0,'0%' UNION SELECT 10, '10%' UNION SELECT 20, '20%' UNION SELECT NULL, 'Без НДС'");
            $res['data_type']['nds']['table']=array();
            foreach($a as $j => $b) $res['data_type']['nds']['table']=$res['data_type']['nds']['table']+array($b[0] => $b[1]);  
            $res['data_type']['nds']['type']="select";

            $a=DBFetchNew($this->conn, "SELECT 'Включен в тариф','Включен в тариф' UNION SELECT 'Добавляется к тарифу', 'Добавляется к тарифу'");
            $res['data_type']['nds_type']['table']=array();
            foreach($a as $j => $b) $res['data_type']['nds_type']['table']=$res['data_type']['nds_type']['table']+array($b[0] => $b[1]);  
            $res['data_type']['nds_type']['type']="select";

            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."companies ORDER BY name");
            $res['data_type']['id_company']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_company']['table']=$res['data_type']['id_company']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."contractor ORDER BY name");
            $res['data_type']['id_contractor']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_contractor']['table']=$res['data_type']['id_contractor']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."contractor_account ORDER BY name");
            $res['data_type']['id_account']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_account']['table']=$res['data_type']['id_account']['table']+array($b[0] => $b[1]);

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

            $arr_name=array('ID','Когда изменил','Кто изменил','Активность','Организация','Контрагент','Название','Номер','Дата начала действия','Дата окончания действия','Дата подписания','НДС %','НДС тип расчета','Дополнительная информация','Счет контрагента');

            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);


            $a=DBFetchNew($this->conn, "SELECT 0,'0%' UNION SELECT 10, '10%' UNION SELECT 20, '20%' UNION SELECT NULL, 'Без НДС'");
            $res['data_type']['nds']['table']=array();
            foreach($a as $j => $b) $res['data_type']['nds']['table']=$res['data_type']['nds']['table']+array($b[0] => $b[1]);  
            $res['data_type']['nds']['type']="select";

            $a=DBFetchNew($this->conn, "SELECT 'Включен в тариф','Включен в тариф' UNION SELECT 'Добавляется к тарифу', 'Добавляется к тарифу'");
            $res['data_type']['nds_type']['table']=array();
            foreach($a as $j => $b) $res['data_type']['nds_type']['table']=$res['data_type']['nds_type']['table']+array($b[0] => $b[1]);  
            $res['data_type']['nds_type']['type']="select";

            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."companies ORDER BY name");
            $res['data_type']['id_company']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_company']['table']=$res['data_type']['id_company']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."contractor ORDER BY name");
            $res['data_type']['id_contractor']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_contractor']['table']=$res['data_type']['id_contractor']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."contractor_account ORDER BY name");
            $res['data_type']['id_account']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_account']['table']=$res['data_type']['id_account']['table']+array($b[0] => $b[1]);
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>