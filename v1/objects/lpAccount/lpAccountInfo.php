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
    if(isset($data['id_worker']))
        $id_worker=$data['id_worker'];  
}

$result=array();

if (count($this->urlData)==1){
    if(strpos($this->urlData[0],'_')!==null){
        list($id,$id_worker)=explode('_',$this->urlData[0]);
    }else
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
                $arr_type[$i]=$type_column;
    			$i++;
			}
            
            $res=array();

            foreach($arr as $key=>$val){
                $res[$val]=NULL;  //html_entity_decode(
                $res['data_type'][$val]['type']=$arr_type[$key];
            }

            $arr_name=array('ID','Изменено','Изменил','Исполнитель','Основной','Безналичные','Номер карты','Название банка','БИК банка','Номер счета','Дополнительная информация','Активный');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT w.id, concat(if(m.sname IS NOT NULL and m.sname!='',m.sname,''),' ',if(m.name IS NOT NULL and m.name!='',m.name,''),' ',if(m.mname IS NOT NULL and m.mname!='',m.mname,'')) FROM ".$pref."lp_worker as w, ".$pref."lp_man as m WHERE m.id=w.id_man");
            $res['data_type']['id_worker']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_worker']['table']=$res['data_type']['id_worker']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT 0, 'Наличные' UNION SELECT 1, 'Безнал'");
            $res['data_type']['type']['table']=array();
            foreach($a as $j => $b) $res['data_type']['type']['table']=$res['data_type']['type']['table']+array($b[0] => $b[1]);    
            $res['data_type']['type']['type']='checkbox';
            
            $a=DBFetchNew($this->conn, "SELECT 1, 'Основной' UNION SELECT 0, 'Не основной'");
            $res['data_type']['main']['table']=array();
            foreach($a as $j => $b) $res['data_type']['main']['table']=$res['data_type']['main']['table']+array($b[0] => $b[1]); 
            $res['data_type']['main']['type']='checkbox';
            $res['data_type']['main']['value']=1;
            if($id_worker>0) $res['id_worker']=$id_worker;

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
            $arr_name=array('ID','Изменено','Изменил','Исполнитель','Основной','Безналичные','Номер карты','Название банка','БИК банка','Номер счета','Дополнительная информация','Активный');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT w.id, concat(if(m.sname IS NOT NULL and m.sname!='',m.sname,''),' ',if(m.name IS NOT NULL and m.name!='',m.name,''),' ',if(m.mname IS NOT NULL and m.mname!='',m.mname,'')) FROM ".$pref."lp_worker as w, ".$pref."lp_man as m WHERE m.id=w.id_man");
            $res['data_type']['id_worker']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_worker']['table']=$res['data_type']['id_worker']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT 0, 'Наличные' UNION SELECT 1, 'Безнал'");
            $res['data_type']['type']['table']=array();
            foreach($a as $j => $b) $res['data_type']['type']['table']=$res['data_type']['type']['table']+array($b[0] => $b[1]);    
            $res['data_type']['type']['type']='checkbox';
            
            $a=DBFetchNew($this->conn, "SELECT 1, 'Основной' UNION SELECT 0, 'Не основной'");
            $res['data_type']['main']['table']=array();
            foreach($a as $j => $b) $res['data_type']['main']['table']=$res['data_type']['main']['table']+array($b[0] => $b[1]); 
            $res['data_type']['main']['type']='checkbox';
            $res['data_type']['main']['value']=1;
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>