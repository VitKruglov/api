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
    if(!is_null($data['data_from_include_api'])){
        $newPerson=$data['data_from_include_api'];
        $db_ak = DBConnect($tmpl, $this->config, $this->urlData[0]."_");
        DBExecuteNew($db_ak, "INSERT INTO ".$this->urlData[0]."_persons (name,password,realname, email,id_unit, id_group) VALUES ('".$newPerson['namePerson']."','".$newPerson['password']."','".$newPerson['namePerson']."','".$newPerson['namePerson']."@ak".$this->urlData[0].".ru',1,1)");
    }

    
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
            $arr_name=array('ID','Создано','Создал','Изменено','Изменил','Закрыто','Закрыл','Название','Домен','Адрес','Граница закрытого периода','Координаты широта','Координаты долгота','Telegram token','Примечание');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            $res['data_type']['dt_begin']['readonly']='readonly';
            $res['data_type']['dt_close']['readonly']='readonly';

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_change']['readonly']='readonly';

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_begin']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_begin']['table']=$res['data_type']['id_person_begin']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_begin']['readonly']='readonly';

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_close']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_close']['table']=$res['data_type']['id_person_close']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_close']['readonly']='readonly';

           
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
            $arr[0]='namePerson';
            $arr_type[0]='varchar';

			while (count($res_columns)>$i)
			{
    			list($name_column,$type_column,$null_column,$key_column,$default_column)=$res_columns[$i];
				$arr[$i+1]=strtolower($name_column);
                $arr_type[$i+1]=$type_column;
    			$i++;
			}

            $arr[$i+1]='newPerson';
            $i++;
            
            $res=array();
            $res['data_type']=array();

            $res['data_type']['newPerson']['type']='button';
            $res['data_type']['newPerson']['name']='';
            $res['newPerson']='Создать пользователя';

            foreach($result as $key=>$val){
                $res[$arr[$key+1]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key+1]]['type']=$arr_type[$key+1];
            }
            $arr_name=array('Имя нового пользователя','ID','Создано','Создал','Изменено','Изменил','Закрыто','Закрыл','Название','Домен','Адрес','Граница закрытого периода','Координаты широта','Координаты долгота','Telegram token','Примечание');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            $res['data_type']['dt_begin']['readonly']='readonly';
            $res['data_type']['dt_close']['readonly']='readonly';

            $res['data_type']['namePerson']['type']='varchar';
            $res['namePerson']='';

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_change']['readonly']='readonly';

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_begin']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_begin']['table']=$res['data_type']['id_person_begin']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_begin']['readonly']='readonly';

            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_close']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_close']['table']=$res['data_type']['id_person_close']['table']+array($b[0] => $b[1]);
            $res['data_type']['id_person_close']['readonly']='readonly';

           
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>