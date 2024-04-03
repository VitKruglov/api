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

            $arr_name=array('ID','Изменено','Изменил','Дата создания','Важность','Физ.лицо','Комментарий','Активный');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,'')) FROM ".$pref."lp_man");
            $res['data_type']['id_man']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_man']['table']=$res['data_type']['id_man']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT 1, 'Обычный' UNION SELECT 2, 'ВАЖНО'");
            $res['data_type']['importance']['table']=array();
            foreach($a as $j => $b) $res['data_type']['importance']['table']=$res['data_type']['importance']['table']+array($b[0] => $b[1]);
            $res['data_type']['importance']['type']='select';

            if($id_man>0) $res['id_man']=$id_man;
            
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
    			$i++;
			}
            
            $res=array();

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

            $arr_name=array('ID','Изменено','Изменил','Дата создания','Важность','Физ.лицо','Комментарий','Активный');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id, concat(if(sname IS NOT NULL and sname!='',sname,''),' ',if(name IS NOT NULL and name!='',name,''),' ',if(mname IS NOT NULL and mname!='',mname,'')) FROM ".$pref."lp_man");
            $res['data_type']['id_man']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_man']['table']=$res['data_type']['id_man']['table']+array($b[0] => $b[1]);  

            $a=DBFetchNew($this->conn, "SELECT 1, 'Обычный' UNION SELECT 2, 'ВАЖНО'");
            $res['data_type']['importance']['table']=array();
            foreach($a as $j => $b) $res['data_type']['importance']['table']=$res['data_type']['importance']['table']+array($b[0] => $b[1]);
            $res['data_type']['importance']['type']='select';
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>