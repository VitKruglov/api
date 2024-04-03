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

                $res[strtolower($name_column)]='';  //html_entity_decode(
                $res['data_type'][strtolower($name_column)]['type']=$type_column;
    			$i++;
			}
            $arr_name=array('ID','Название','Изменено','Изменил','Примечание','Создан автоматически','Активный');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);

            $a=DBFetchNew($this->conn, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'");
            $res['data_type']['auto']['table']=array();
            foreach($a as $j => $b) $res['data_type']['auto']['table']=$res['data_type']['auto']['table']+array($b[0] => $b[1]); 
            $res['data_type']['auto']['type']='checkbox';

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

            $arr[$i]='nameReq';
            $i++;
            $arr[$i]='hiddenYes';
            $i++;
            $arr[$i]='hiddenNo';
            $i++;
            $arr[$i]='requiredYes';
            $i++;
            $arr[$i]='requiredNo';
            $i++;
            $arr[$i]='newreq';
            $i++;
            $arr[$i]='reqs';
            $i++;
            
            $res=array();

            foreach($result as $key=>$val){
                $res[$arr[$key]]=$val;  //html_entity_decode(
                $res['data_type'][$arr[$key]]['type']=$arr_type[$key];
            }

            //данные для добавления требования в набор
            $a=DBFetchNew($this->conn, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'");
            $res['data_type']['auto']['table']=array();
            foreach($a as $j => $b) $res['data_type']['auto']['table']=$res['data_type']['auto']['table']+array($b[0] => $b[1]); 
            $res['data_type']['auto']['type']='checkbox';

            $a=DBFetchNew($this->conn, "SELECT id,concat(name, if(hidden=1,' (скрыто)',''), if(required=1,' (строго)','')) FROM ".$pref."global_requirements WHERE id NOT IN (SELECT id_requirement FROM ".$pref."set_req WHERE id_set=$id) ORDER BY name");
            $res['data_type']['nameReq']['table']=array();
            foreach($a as $j => $b) $res['data_type']['nameReq']['table']=$res['data_type']['nameReq']['table']+array($b[0] => $b[1]);
            $res['data_type']['nameReq']['type']='select';

            $a=DBFetchNew($this->conn, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'");
            $res['data_type']['hiddenYes']['table']=array();
            foreach($a as $j => $b) $res['data_type']['hiddenYes']['table']=$res['data_type']['hiddenYes']['table']+array($b[0] => $b[1]); 
            $res['data_type']['hiddenYes']['type']='checkbox';

            $a=DBFetchNew($this->conn, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'");
            $res['data_type']['hiddenNo']['table']=array();
            foreach($a as $j => $b) $res['data_type']['hiddenNo']['table']=$res['data_type']['hiddenNo']['table']+array($b[0] => $b[1]); 
            $res['data_type']['hiddenNo']['type']='checkbox';

            $a=DBFetchNew($this->conn, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'");
            $res['data_type']['requiredYes']['table']=array();
            foreach($a as $j => $b) $res['data_type']['requiredYes']['table']=$res['data_type']['requiredYes']['table']+array($b[0] => $b[1]); 
            $res['data_type']['requiredYes']['type']='checkbox';

            $a=DBFetchNew($this->conn, "SELECT 1, 'Да' UNION SELECT 0, 'Нет'");
            $res['data_type']['requiredNo']['table']=array();
            foreach($a as $j => $b) $res['data_type']['requiredNo']['table']=$res['data_type']['requiredNo']['table']+array($b[0] => $b[1]); 
            $res['data_type']['requiredNo']['type']='checkbox';

            //виды требований
            $reqs=DBFetchNew($this->conn, "SELECT tbl2.name, tbl3.name, tbl4.name, concat('<input type=\"submit\" class=\"btn btn-link\" name=\"submit\" value=\"удалить ',tbl1.id_requirement,'\">')
            FROM ".$pref."set_req as tbl1
            LEFT JOIN ".$pref."global_requirements as tbl2 ON tbl2.id=tbl1.id_requirement
            LEFT JOIN (SELECT 1 as id, 'Да' as name UNION SELECT 0,'Нет') as tbl3 ON tbl3.id=tbl1.hidden
            LEFT JOIN (SELECT 1 as id, 'Да' as name UNION SELECT 0,'Нет') as tbl4 ON tbl4.id=tbl1.required
            WHERE id_set=$id");

            $res['data_type']['newreq']['type']='button';
            $res['newreq']='Добавить требование';

            $res['reqs']=$reqs;
            $res['reqs']=$res['reqs']+array('name'=>array('Требование','Скрытое','Строгое',''));
            $res['data_type']['reqs']['type']='table';   

            $arr_name=array('ID','Название','Изменено','Изменил','Примечание','Создан автоматически','Активный', 'Выбери требование','Скрытое','Не скрытое','Строгое','Не строгое','','Список требований');
            foreach($arr_name as $i=>$name)
                $res['data_type'][$arr[$i]]['name']=$name;

            //данные из перекресных таблиц
            $a=DBFetchNew($this->conn, "SELECT id,realname FROM ".$pref."persons ORDER BY realname");
            $res['data_type']['id_person_change']['table']=array();
            foreach($a as $j => $b) $res['data_type']['id_person_change']['table']=$res['data_type']['id_person_change']['table']+array($b[0] => $b[1]);
            
            $this->result=$res;
        }else{
            $this->result=array('message'=>404);
        }
    }
}




?>