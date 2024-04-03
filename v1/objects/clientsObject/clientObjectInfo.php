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
    if(!is_null($data['data_from_include_api']))
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
            $arr_name=array('ID','Название','Группа','Адрес','Адрес ФИАС','Координаты широта','Координаты долгота','Часовой пояс относительно UTC+0(+/-)','Внимание','Дополнительная информация','Активность','Граница закрытого периода','Кто изменил границу закрытого прерида','Когда изменил границу закрытого периода','Кто закрыл','Когда закрыл','Кто изменил','Когда изменил','Ответственный менеджер');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person']['table']=$res['data_type']['id_person']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."clients_group ORDER BY name");
            $res['data_type']['id_group']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_group']['table']=$res['data_type']['id_group']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT id,address FROM ".$pref."fias WHERE level=8 ORDER BY address");
            $res['data_type']['id_fiass']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_fiass']['table']=$res['data_type']['id_fiass']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_close']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_close']['table']=$res['data_type']['id_person_close']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_close_period']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_close_period']['table']=$res['data_type']['id_person_close_period']['table']+array($b[0] => $b[1]);

            $res['data_type']['id_person_close_period']['readonly']='readonly';
            $res['data_type']['dt_close_period']['readonly']='readonly';
            $res['data_type']['id_person_close']['readonly']='readonly';
            $res['data_type']['dt_close']['readonly']='readonly';
            
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

            $arr[$i]='fiassearch';
            $i++;
            $arr[$i]='newfias';
            $i++;
            
            $res=array();

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

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

            $arr_name=array('ID','Название','Группа','Адрес','Адрес ФИАС','Координаты широта','Координаты долгота','Часовой пояс относительно UTC+0(+/-)','Внимание','Дополнительная информация','Активность','Граница закрытого периода','Кто изменил границу закрытого прерида','Когда изменил границу закрытого периода','Кто закрыл','Когда закрыл','Кто изменил','Когда изменил','Ответственный менеджер','','');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person']['table']=$res['data_type']['id_person']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,name FROM ".$pref."clients_group ORDER BY name");
            $res['data_type']['id_group']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_group']['table']=$res['data_type']['id_group']['table']+array($b[0] => $b[1]);
  
            $a=DBFetchNew($this->conn, "SELECT id,address FROM ".$pref."fias WHERE level=8 ORDER BY address");
            $res['data_type']['id_fiass']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_fiass']['table']=$res['data_type']['id_fiass']['table']+array($b[0] => $b[1]);
            
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_close']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_close']['table']=$res['data_type']['id_person_close']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_close_period']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_close_period']['table']=$res['data_type']['id_person_close_period']['table']+array($b[0] => $b[1]);

            $res['data_type']['id_person_close_period']['readonly']='readonly';
            $res['data_type']['dt_close_period']['readonly']='readonly';
            $res['data_type']['id_person_close']['readonly']='readonly';
            $res['data_type']['dt_close']['readonly']='readonly';

            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>