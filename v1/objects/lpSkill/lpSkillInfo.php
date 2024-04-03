<?php
//-------------------------------------------------------------//
//                                                             //
//        получение информации о навыке по ID                 //
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
            $arr_name=array('ID','Изменено','Изменил','Активный','Навык','Исполнитель','Клиент','Примечание');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id, concat(name,if(clients=1,' (клиентозависимый)','')) FROM ".$pref."global_requirements ");
            $res['data_type']['id_skill']['table']=array();
            $res['data_type']['id_skill']['type']='select';
            foreach($a as $j => $b) $res['data_type']['id_skill']['table']=$res['data_type']['id_skill']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT NULL, NULL UNION SELECT id, name FROM ".$pref."clients");
            $res['data_type']['id_client']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_client']['table']=$res['data_type']['id_client']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT tbl1.id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,''),if(bd IS NOT NULL and bd!='',concat(' (',bd,')'),'')) FROM ".$pref."lp_worker as tbl1, ".$pref."lp_man as tbl2 WHERE tbl2.id=tbl1.id_man");
            $res['data_type']['id_worker']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_worker']['table']=$res['data_type']['id_worker']['table']+array($b[0] => $b[1]);  
                
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
            $arr_name=array('ID','Изменено','Изменил','Активный','Навык','Исполнитель','Клиент','Примечание');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id, concat(name,if(clients=1,' (клиентозависимый)','')) FROM ".$pref."global_requirements ");
            $res['data_type']['id_skill']['table']=array();
            $res['data_type']['id_skill']['type']='select';
            foreach($a as $j => $b) $res['data_type']['id_skill']['table']=$res['data_type']['id_skill']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT NULL, NULL UNION SELECT id, name FROM ".$pref."clients");
            $res['data_type']['id_client']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_client']['table']=$res['data_type']['id_client']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT tbl1.id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,''),if(bd IS NOT NULL and bd!='',concat(' (',bd,')'),'')) FROM ".$pref."lp_worker as tbl1, ".$pref."lp_man as tbl2 WHERE tbl2.id=tbl1.id_man");
            $res['data_type']['id_worker']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_worker']['table']=$res['data_type']['id_worker']['table']+array($b[0] => $b[1]);  
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>